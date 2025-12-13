<?php
namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Requests\Property\ReservePropertyRequest;
use App\Services\PropertyService;
use App\Services\Logger;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;
    protected Logger $logger;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
        $this->logger = Logger::getInstance();
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display property management page
     */
    public function propertyManagement()
    {
        $user = auth()->user();

        try {
            $this->logger->info('Property management accessed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ]);

            $properties = $this->propertyService->getPropertiesForManagement($user);
            $canAssignOwner = $user->role === 'admin';

            return view('properties.property-management', compact('properties', 'canAssignOwner'));
        } catch (\Exception $e) {
            $this->logger->error('Property management page failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to load property management page.');
        }
    }

    /**
     * Show all properties (with filters/search)
     */
    public function index(Request $request)
    {
        try {
            $this->logger->info('Property index accessed', [
                'user_id' => auth()->id(),
                'filters' => $request->only(['search_term', 'category', 'location', 'min_price', 'max_price', 'transaction_type', 'sort_by']),
            ]);

            $filters = $request->only(['search_term', 'category', 'location', 'min_price', 'max_price', 'transaction_type', 'sort_by']);
            $properties = $this->propertyService->getPropertiesWithFilters($filters);
            
            $categories = Property::select('category')->distinct()->pluck('category');
            $locations = Property::select('location')->distinct()->pluck('location');

            // Get property counts by status
            $statusCounts = [
                'available' => Property::where('status', 'available')->count(),
                'sold' => Property::where('status', 'sold')->count(),
                'pending' => Property::where('status', 'pending')->count(),
                'reserved' => Property::whereHas('reservation')->count(),
            ];

            return view('properties.index', compact('properties', 'categories', 'locations', 'statusCounts'));
        } catch (\Exception $e) {
            $this->logger->error('Property index failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to load properties.');
        }
    }
    

    // Show single property
    public function show(Property $property)
    {
        $property->load(['images' => function($query) {
            $query->orderBy('id', 'asc');
        }, 'reservation']); // eager load multiple images + reservation info

        // If AJAX request, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'property' => $property,
                'images' => $property->images->map(function($img) {
                    return [
                        'id' => $img->id,
                        'image_path' => $img->image_path,
                    ];
                })->values()
            ]);
        }

        $reservation = $property->reservation;
        $user = auth()->user(); // Can be null for guests

        // Guests can view but cannot reserve
        $canReserve = $user && (!$reservation) && $property->status === 'available';
        $canCancel = $reservation && $user && ($reservation->user_id === $user->id || $user->role === 'admin');
        $isPending = $reservation && !$canCancel;
        $isSold = $property->status === 'sold';

        return view('properties.details', compact('property', 'reservation', 'canReserve', 'canCancel', 'isPending', 'isSold'));
    }

    /**
     * Return property images as JSON (for edit modal / management).
     */
    public function images(Property $property)
    {
        // Reload images to ensure we get all of them
        $property->load(['images' => function($query) {
            $query->orderBy('id', 'asc');
        }]);

        return response()->json([
            'primary_image' => $property->image,
            'images' => $property->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'image_path' => $img->image_path,
                ];
            })->values(), // Use values() to ensure array indices are sequential
        ]);
    }


    // Show create form
    public function create()
    {
        return view('properties.create');
    }

    /**
     * Store new property
     */
    public function store(StorePropertyRequest $request)
    {
        try {
            $data = $request->validated();
            $image = $request->hasFile('image') ? $request->file('image') : null;
            $multipleImages = $request->hasFile('multiple_images') ? $request->file('multiple_images') : null;

            $property = $this->propertyService->createProperty($data, $image, $multipleImages);

            return response()->json(['success' => true, 'property' => $property], 201);
        } catch (\Exception $e) {
            $this->logger->error('Property creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => 'Creation failed'], 500);
        }
    }

    // Edit form
    public function edit(Property $property)
    {
        $property->load('images');
        return view('properties.edit', compact('property'));
    }

    /**
     * Update property
     */
    public function update(UpdatePropertyRequest $request, Property $property)
    {
        try {
            $data = $request->validated();
            $image = $request->hasFile('image') ? $request->file('image') : null;
            $multipleImages = $request->hasFile('multiple_images') ? $request->file('multiple_images') : null;
            $deletedImages = $request->input('deleted_images');

            $property = $this->propertyService->updateProperty($property, $data, $image, $multipleImages, $deletedImages);

            // Reload property with all images
            $property->load(['images' => function($query) {
                $query->orderBy('id', 'asc');
            }]);

            return response()->json(['success' => true, 'property' => $property]);
        } catch (\Exception $e) {
            $this->logger->error('Property update failed', [
                'property_id' => $property->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Update failed'], 500);
        }
    }

    /**
     * Delete individual property image
     */
    public function deleteImage(Request $request, PropertyImage $propertyImage)
    {
        try {
            $property = $propertyImage->property;
            
            // Check if user owns the property or is admin
            $user = auth()->user();
            if ($user->role !== 'admin' && $property->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $this->propertyService->deletePropertyImage($propertyImage);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $this->logger->error('Property image deletion failed', [
                'image_id' => $propertyImage->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Deletion failed'], 500);
        }
    }

    /**
     * Delete property
     */
    public function destroy(Request $request, Property $property)
    {
        try {
            $this->propertyService->deleteProperty($property);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $this->logger->error('Property deletion failed', [
                'property_id' => $property->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Deletion failed'], 500);
        }
    }
    /**
     * Reserve a property
     */
    public function reserve(ReservePropertyRequest $request, Property $property)
    {
        try {
            $data = $request->validated();
            $this->propertyService->reserveProperty($property, $data);

            return redirect()->back()->with('success', 'Property reserved successfully! Check your email for confirmation details.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check your reservation details and try again.');
        } catch (\Exception $e) {
            $this->logger->error('Property reservation failed', [
                'property_id' => $property->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    /**
     * Cancel property reservation
     */
    public function cancelReservation(Property $property)
    {
        try {
            $this->propertyService->cancelReservation($property);
            
            return redirect()->back()->with('success', 'Reservation cancelled successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Reservation cancellation failed', [
                'property_id' => $property->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function paymentPlans(Property $property)
{
    return view('properties.payment_plans', compact('property'));
}

    /**
     * Reset reservation info so the property becomes unclaimed.
     */
    protected function releaseReservation(Property $property): void
    {
        $property->reservation?->delete();

    // اجعل حالة العقار متاحة بعد إزالة الحجز
        if ($property->status !== 'sold') {
        $property->update(['status' => 'available']);
    }
    }
}
