<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyReservation;
use App\Services\ReservationService;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Property\UpdatePropertyRequest;

class PropertyController extends Controller
{
    protected ReservationService $reservationService;
    protected PropertyService $service;

    public function __construct(ReservationService $reservationService, PropertyService $service)
    {
        $this->reservationService = $reservationService;
        $this->service = $service;

        // Apply auth middleware only for reserve/cancel
        $this->middleware('auth')->only(['reserve', 'cancelReservation']);
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'transaction_type', 'search_term', 'category', 'location', 'min_price', 'max_price', 'sort_by'
        ]);

        $properties = $this->service->search($filters);

        $categories = Property::select('category')->distinct()->pluck('category');
        $locations = Property::select('location')->distinct()->pluck('location');

        return view('properties.index', compact('properties', 'categories', 'locations'));
    }

    public function json(Request $request)
{
    $filters = $request->only([
        'transaction_type', 'search_term', 'category', 'location', 'min_price', 'max_price', 'sort_by'
    ]);

    $properties = $this->service->search($filters);

    $properties->map(function ($prop) {
        // Ensure main image exists
        $prop->image_url = $this->service->ensureImage($prop);

        // Ensure multiple images exist
        $this->service->ensureMultipleImages($prop);

        return $prop;
    });

    return response()->json($properties);
}


    public function create()
    {
        return view('properties.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $image = $request->file('image');
        $multipleImages = $request->file('multiple_images');

        $property = $this->service->createProperty($data, $image, $multipleImages);

        // Return JSON if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Property created successfully.',
                'property' => $property
            ]);
        }

        return redirect()->route('properties.index')->with('success', 'Property created successfully.');
    }

    public function show(Property $property)
    {
        $property->load('images', 'reservation');

        $reservation = $property->reservation;

        $isSold = $property->status === 'sold';
        $isPending = $reservation && $reservation->status === 'pending';
        $canReserve = Auth::check() && !$reservation && !$isSold;
        $canCancel = Auth::check() && $reservation && $reservation->user_id === Auth::id() && $reservation->status === 'pending';

        return view('properties.details', compact(
            'property', 'reservation', 'isSold', 'isPending', 'canReserve', 'canCancel'
        ));
    }

    public function edit(Property $property)
    {
        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $data = $request->all();
        $image = $request->file('image');
        $multipleImages = $request->file('multiple_images');

        $this->service->updateProperty($property, $data, $image, $multipleImages);

        return redirect()->route('property-management')->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property)
{
    $this->service->deleteProperty($property);

    if(request()->ajax()) {
        return response()->json(['success' => true]);
    }

    return redirect()->route('property-management')->with('success', 'Property deleted successfully.');
}


    // Reserve a property via AJAX
 public function reserve(Property $property, Request $request)
{
    if (!Auth::check()) {
        return $request->ajax()
            ? response()->json(['message' => 'Please login'], 401)
            : redirect()->route('login.form');
    }

    if ($property->status === 'sold' || $property->status === 'reserved') {
        return $request->ajax()
            ? response()->json(['message' => 'Property is not available'], 422)
            : redirect()->back()->with('error', 'Property is not available');
    }

    DB::transaction(function() use ($property) {
        // Update property status
        $property->update([
            'status' => 'reserved',
        ]);

        // Track reservation
        PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => Auth::id(),
            'reserved_at' => now(),
        ]);
    });

    if ($request->ajax()) {
        return response()->json(['message' => 'Property reserved successfully']);
    }

    return redirect()->back()->with('success', 'Property reserved successfully.');
}

// Cancel reservation
public function cancelReservation(Property $property, Request $request)
{
    if (!Auth::check()) {
        return $request->ajax()
            ? response()->json(['message' => 'Please login'], 401)
            : redirect()->route('login.form');
    }

    $reservation = PropertyReservation::where('property_id', $property->id)
                    ->where('user_id', Auth::id())
                    ->first();

    if (!$reservation) {
        return $request->ajax()
            ? response()->json(['message' => 'No reservation found'], 404)
            : redirect()->back()->with('error', 'No reservation found');
    }

    DB::transaction(function() use ($property, $reservation) {
        // Update property status back to available
        $property->update(['status' => 'available']);

        // Delete reservation record
        $reservation->delete();
    });

    if ($request->ajax()) {
        return response()->json(['message' => 'Reservation cancelled successfully']);
    }

    return redirect()->back()->with('success', 'Reservation cancelled successfully.');
}




    public function propertyManagement()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $properties = Property::with('images')->get();
        } else {
            $properties = Property::with('images')->where('user_id', $user->id)->get();
        }

        return view('properties.property-management', compact('properties'));
    }
}

