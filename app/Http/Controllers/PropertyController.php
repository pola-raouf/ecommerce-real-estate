<?php
namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\OwnsProperty;
use App\Models\PropertyReservation;
use App\Http\Middleware\Role;
use App\Services\Logger;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware(OwnsProperty::class)->only(['edit', 'update', 'destroy']);
    }

    // Display property management page
    public function propertyManagement()
    {
        $logger = Logger::getInstance();
        $user = auth()->user();

         try {
        $logger->info('Property management accessed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    } catch (\Exception $e) {
        $logger->error('Logging failed on property management access', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
        ]);
    }
        
        $properties = Property::query()
            ->when($user->role === 'seller', fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('id')
            ->get();

        $canAssignOwner = $user->role === 'admin';

        return view('properties.property-management', compact('properties', 'canAssignOwner'));
    }

    // Show all properties (with filters/search)
    public function index(Request $request)
    {
        $logger=Logger::getInstance();
        try {
        $logger->info('Property index accessed', [
            'user_id' => auth()->id(),
            'search_term' => $request->search_term ?? null,
            'category' => $request->category ?? null,
            'location' => $request->location ?? null,
            'min_price' => $request->min_price ?? null,
            'max_price' => $request->max_price ?? null,
            'transaction_type' => $request->transaction_type ?? null,
            'sort_by' => $request->sort_by ?? null,
        ]);
    } catch (\Exception $e) {
        $logger->error('Logging failed on property index access', [
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);
    }
        
        $query = Property::query();

        if ($request->filled('search_term')) {
    $term = $request->search_term;
    $query->where(function ($q) use ($term) {
        $q->where('category', 'like', "%$term%")
          ->orWhere('location', 'like', "%$term%");
          
        // تحقق إن البحث عن رقم قبل المقارنة مع id
        if (is_numeric($term)) {
            $q->orWhere('id', $term);
        }
    });
}

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('location')) $query->where('location', $request->location);
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('transaction_type')) $query->where('transaction_type', $request->transaction_type);

        if ($request->filled('sort_by')) {
            [$col, $dir] = explode(' ', $request->sort_by);
            $query->orderBy($col, $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $properties = $query->get();
        $categories = Property::select('category')->distinct()->pluck('category');
        $locations = Property::select('location')->distinct()->pluck('location');

        return view('properties.index', compact('properties', 'categories', 'locations'));
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
        $user = auth()->user();

        $canReserve = (!$reservation) && $property->status === 'available';
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

    // Store new property
    public function store(Request $request)
    {
        $logger = Logger::getInstance();
        
        $data = $request->validate([
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,available,sold,reserved',
            'description' => 'nullable|string',
            'transaction_type' => 'required|string|in:sale,rent',
            'installment_years' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'user_id' => 'nullable|exists:users,id',
            'multiple_images' => 'nullable|array',
            'multiple_images.*' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try{
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $data['user_id'] = ($user->role === 'admin' && !empty($data['user_id']))
            ? $data['user_id']
            : $user->id;
            
        // Ensure description is not null
        if (empty($data['description'])) {
            $data['description'] = '';
        }

        // Handle single image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/properties'), $filename);
            $data['image'] = 'images/properties/'.$filename;
        }
        // Ensure image field has a value to satisfy non-null DB column
        if (!isset($data['image'])) {
            $data['image'] = '';
        }

        $property = Property::create($data);

        // Handle multiple images
        if ($request->hasFile('multiple_images')) {
            $files = $request->file('multiple_images');
            // Ensure it's an array
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                // Skip if file is invalid, empty, or not uploaded
                if (!$file || !$file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
                    continue;
                }
                
                // Validate file type
                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    continue;
                }
                
                $filename = time().'_'.uniqid().'_'.$file->getClientOriginalName();
                $file->move(public_path('images/properties'), $filename);

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => 'images/properties/'.$filename,
                ]);
            }
        }
            $logger->info('Property created successfully', ['property_id' => $property->id, 'user_id' => $user->id]);

        return response()->json(['success' => true, 'property' => $property], 201);
        } catch (\Exception $e) {
        $logger->error('Property creation failed', ['error' => $e->getMessage(), 'user_id' => auth()->id() ?? null]);
        return response()->json(['error' => 'Creation failed'], 500);
    }
    }

    // Edit form
    public function edit(Property $property)
    {
        $property->load('images');
        return view('properties.edit', compact('property'));
    }

    // Update property
    public function update(Request $request, Property $property)
    {
        $logger = \App\Services\Logger::getInstance();

        try {
        $data = $request->validate([
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pending,available,sold,reserved',
            'description' => 'nullable|string',
            'transaction_type' => 'required|string|in:sale,rent',
            'installment_years' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'user_id' => 'nullable|exists:users,id',
            'multiple_images' => 'nullable|array',
            'multiple_images.*' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'nullable|integer|exists:property_images,id',
        ]);

        // Single image
        if ($request->hasFile('image')) {
            if ($property->image && file_exists(public_path($property->image))) {
                unlink(public_path($property->image));
            }
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/properties'), $filename);
            $data['image'] = 'images/properties/'.$filename;
        }
        // If no new image uploaded, keep existing or set empty string to avoid null constraint
        if (!isset($data['image'])) {
            $data['image'] = $property->image ?? '';
        }

        $shouldResetReservation = isset($data['status']) && $data['status']==='available';

        if (isset($data['user_id']) && auth()->user()->role !== 'admin') {
            unset($data['user_id']);
        }

        $property->update($data);

        if ($shouldResetReservation) {
            $this->releaseReservation($property);
        }

        $property->refresh();

        // Handle deleted images
        if ($request->has('deleted_images') && is_array($request->deleted_images)) {
            foreach ($request->deleted_images as $imageId) {
                $image = PropertyImage::find($imageId);
                if ($image && $image->property_id === $property->id) {
                    if (file_exists(public_path($image->image_path))) {
                        @unlink(public_path($image->image_path));
                    }
                    $image->delete();
                }
            }
        }

        // Multiple images
        if ($request->hasFile('multiple_images')) {
            $files = $request->file('multiple_images');
            // Ensure it's an array
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                // Skip if file is invalid, empty, or not uploaded
                if (!$file || !$file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
                    continue;
                }
                
                // Validate file type
                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    continue;
                }
                
                $filename = time().'_'.uniqid().'_'.$file->getClientOriginalName();
                $file->move(public_path('images/properties'), $filename);

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => 'images/properties/'.$filename,
                ]);
            }
        }

             $logger->info('Property updated successfully', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'updated_data' => $data,
        ]);

        // Reload property with all images
        $property->refresh();
        $property->load(['images' => function($query) {
            $query->orderBy('id', 'asc');
        }]);

        return response()->json(['success' => true, 'property' => $property]);
        } catch (\Exception $e) {
        $logger->error('Property update failed', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);

        return response()->json(['error' => 'Update failed'], 500);
    }
    }

    // Delete individual property image
    public function deleteImage(Request $request, PropertyImage $propertyImage)
    {
        $logger = \App\Services\Logger::getInstance();

        try {
            $property = $propertyImage->property;
            
            // Check if user owns the property or is admin
            $user = auth()->user();
            if ($user->role !== 'admin' && $property->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if (file_exists(public_path($propertyImage->image_path))) {
                @unlink(public_path($propertyImage->image_path));
            }

            $propertyImage->delete();

            $logger->info('Property image deleted successfully', [
                'image_id' => $propertyImage->id,
                'property_id' => $property->id,
                'user_id' => $user->id,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $logger->error('Property image deletion failed', [
                'image_id' => $propertyImage->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Deletion failed'], 500);
        }
    }

    // Delete property
    public function destroy(Request $request, Property $property)
    {
        $logger = \App\Services\Logger::getInstance();

        try {
        if ($property->image && file_exists(public_path($property->image))) {
            @unlink(public_path($property->image));
        }

        // Delete multiple images
        foreach ($property->images as $img) {
            if (file_exists(public_path($img->image_path))) {
                @unlink(public_path($img->image_path));
            }
            $img->delete();
        }

        $property->delete();

            $logger->info('Property deleted successfully', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
        ]);
        return response()->json(['success' => true]);
        } catch (\Exception $e) {
        $logger->error('Property deletion failed', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);

        return response()->json(['error' => 'Deletion failed'], 500);
    }
    }
    public function reserve(Request $request, Property $property)
{
    $logger = \App\Services\Logger::getInstance();
     try {
        $userId = auth()->id();

    // Check if property is already reserved
    if ($property->isReserved()) {
        $logger->warning('Property reservation failed - already reserved', [
                'property_id' => $property->id,
                'user_id' => $userId,
            ]);
            return redirect()->back()->with('error', 'Property already reserved.');
    }

    // Validate based on property type
    if ($property->transaction_type === 'rent') {
        $validated = $request->validate([
            'meeting_datetime' => 'required|date|after:now',
            'start_date' => 'required|date|after:meeting_datetime',
            'duration_value' => 'required|integer|min:1|max:100',
            'duration_unit' => 'required|in:weeks,months,years',
            'notes' => 'nullable|string|max:500',
        ]);
    } else {
        // Sale property - simpler validation
        $validated = $request->validate([
            'meeting_datetime' => 'required|date|after:now',
            'notes' => 'nullable|string|max:500',
        ]);
    }

    // Create reservation with all data
    $reservationData = [
        'property_id' => $property->id,
        'user_id' => $userId,
        'reserved_at' => now(),
        'meeting_datetime' => $validated['meeting_datetime'],
        'notes' => $validated['notes'] ?? null,
    ];

    // Add rental-specific fields if applicable
    if ($property->transaction_type === 'rent') {
        $reservationData['start_date'] = $validated['start_date'];
        $reservationData['duration_value'] = $validated['duration_value'];
        $reservationData['duration_unit'] = $validated['duration_unit'];
    }

    // Create the reservation (Observer will automatically send emails)
    PropertyReservation::create($reservationData);

    // Update property status
    $property->update([
        'status' => 'reserved',
    ]);

$logger->info('Property reserved successfully', [
            'property_id' => $property->id,
            'user_id' => $userId,
            'transaction_type' => $property->transaction_type,
        ]);
    return redirect()->back()->with('success', 'Property reserved successfully! Check your email for confirmation details.');
    
     } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput()
            ->with('error', 'Please check your reservation details and try again.');
            
     } catch (\Exception $e) {
        $logger->error('Property reservation failed', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);

        return redirect()->back()->with('error', 'Failed to reserve property. Please try again.');
    }
}
public function cancelReservation(Property $property)
{
    $logger = \App\Services\Logger::getInstance();
try {
    $reservation = $property->reservation;

    if (!$reservation) {
        return redirect()->back()->with('error', 'No reservation found for this property.');
    }

    $user = auth()->user();
    if ($reservation->user_id !== $user->id && $user->role !== 'admin') {
        abort(403, 'You are not allowed to cancel this reservation.');
    }

    $reservation->delete();
    $property->update(['status' => 'available']);

    $logger->info('Reservation cancelled successfully', [
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);
    
    return redirect()->back()->with('success', 'Reservation cancelled successfully.');
} catch (\Exception $e) {
        $logger->error('Reservation cancellation failed', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);

        return redirect()->back()->with('error', 'Failed to cancel reservation.');
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
        // if ($property->reserved_by) {
        //     $user = User::find($property->reserved_by);

        //     if ($user && $user->reserved_property_id === $property->id) {
        //         $user->reserved_property_id = null;
        //         $user->save();
        //     }
        // }

        // $property->forceFill([
        //     'reserved_by' => null,
        //     'is_reserved' => false,
        // ])->save();
        $property->reservation?->delete();

    // اجعل حالة العقار متاحة بعد إزالة الحجز
        if ($property->status !== 'sold') {
        $property->update(['status' => 'available']);
    }
    //$property->update(['status' => 'available']);
    }
}
