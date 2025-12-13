<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyReservation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PropertyService
{
    protected Logger $logger;
//l
    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Create a new property
     */
    public function createProperty(array $data, ?UploadedFile $image = null, ?array $multipleImages = null): Property
    {
        $data['user_id'] = auth()->user()->role === 'admin' && !empty($data['user_id'])
            ? $data['user_id']
            : auth()->id();

        if ($image) {
            $data['image'] = $this->storeImage($image);
        }

        $property = Property::create($data);

        if ($multipleImages) {
            $this->storeMultipleImages($property, $multipleImages);
        }

        $this->logger->info('Property created successfully', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
        ]);

        return $property;
    }

    /**
     * Update an existing property
     */
    public function updateProperty(Property $property, array $data, ?UploadedFile $image = null, ?array $multipleImages = null, ?array $deletedImages = null): Property
    {
        // Only admin can change user_id
        if (isset($data['user_id']) && auth()->user()->role !== 'admin') {
            unset($data['user_id']);
        }

        // Replace image if uploaded
        if ($image) {
            $this->deleteFile($property->image);
            $data['image'] = $this->storeImage($image);
        }

        // Handle deleted images
        if ($deletedImages && is_array($deletedImages)) {
            foreach ($deletedImages as $imageId) {
                $img = PropertyImage::find($imageId);
                if ($img && $img->property_id === $property->id) {
                    $this->deleteFile($img->image_path);
                    $img->delete();
                }
            }
        }

        // Apply ALL data (including status)
        $property->fill($data);
        $property->save();

        // Add more images
        if ($multipleImages) {
            $this->storeMultipleImages($property, $multipleImages);
        }

        // If marked sold → remove reservation
        if (($data['status'] ?? null) === 'sold') {
            $property->releaseReservation();
        }

        $this->logger->info('Property updated successfully', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
        ]);

        return $property->refresh();
    }


    /**
     * Delete a property
     */
    public function deleteProperty(Property $property): void
    {
        $this->deleteFile($property->image);

        foreach ($property->images as $img) {
            $this->deleteFile($img->image_path);
            $img->delete();
        }

        $property->delete();

        $this->logger->info('Property deleted successfully', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Delete individual property image
     */
    public function deletePropertyImage(PropertyImage $image): void
    {
        $this->deleteFile($image->image_path);
        $image->delete();

        $this->logger->info('Property image deleted successfully', [
            'image_id' => $image->id,
            'property_id' => $image->property_id,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Reserve a property
     */
    public function reserveProperty(Property $property, array $data): PropertyReservation
    {
        // Check if property is already reserved
        if ($property->isReserved()) {
            $this->logger->warning('Property reservation failed - already reserved', [
                'property_id' => $property->id,
                'user_id' => auth()->id(),
            ]);
            throw new \Exception('This property is already reserved.');
        }

        // Create reservation data
        $reservationData = [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'reserved_at' => now(),
            'meeting_datetime' => $data['meeting_datetime'],
            'notes' => $data['notes'] ?? null,
        ];

        // Add rental-specific fields if applicable
        if ($property->transaction_type === 'rent') {
            $reservationData['start_date'] = $data['start_date'];
            $reservationData['duration_value'] = $data['duration_value'];
            $reservationData['duration_unit'] = $data['duration_unit'];
        }

        // Create the reservation (Observer will automatically send emails)
        $reservation = PropertyReservation::create($reservationData);

        // Update property status
        $property->update(['status' => 'reserved']);

        $this->logger->info('Property reserved successfully', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'transaction_type' => $property->transaction_type,
        ]);

        return $reservation;
    }

    /**
     * Cancel a property reservation
     */
    public function cancelReservation(Property $property): void
    {
        $reservation = $property->reservation;

        if (!$reservation) {
            throw new \Exception('No reservation found for this property.');
        }

        $user = auth()->user();
        if ($reservation->user_id !== $user->id && $user->role !== 'admin') {
            throw new \Exception('You are not allowed to cancel this reservation.');
        }

        $reservation->delete();
        $property->update(['status' => 'available']);

        $this->logger->info('Reservation cancelled successfully', [
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Get properties for management page based on user role
     */
    public function getPropertiesForManagement($user)
    {
        $query = Property::query();

        // If user is a seller, only show their properties
        if ($user->role === 'seller') {
            $query->where('user_id', $user->id);
        }

        return $query->orderByDesc('id')->get();
    }

    /**
     * Get properties with filters for index page
     */
    public function getPropertiesWithFilters(array $filters)
    {
        $query = Property::query();

        // Search term
        if (!empty($filters['search_term'])) {
            $term = $filters['search_term'];
            $query->where(function ($q) use ($term) {
                $q->where('category', 'like', "%$term%")
                  ->orWhere('location', 'like', "%$term%");
                  
                // Check if search term is numeric for ID search
                if (is_numeric($term)) {
                    $q->orWhere('id', $term);
                }
            });
        }

        // Category filter
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Location filter
        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        // Price range filters
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Transaction type filter
        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        // Sorting
        if (!empty($filters['sort_by'])) {
            [$col, $dir] = explode(' ', $filters['sort_by']);
            $query->orderBy($col, $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query->get();
    }

    /**
     * Ensure main image exists in public folder
     */
    public function ensureImage(Property $property): string
    {
        $placeholder = 'images/properties/placeholder.jpg';

        if (!$property->image) return $placeholder;

        $filename = basename($property->image);
        $storagePath = storage_path('app/public/properties/' . $filename);
        $publicPath = public_path('images/properties/' . $filename);

        if (!file_exists($publicPath) && file_exists($storagePath)) {
            copy($storagePath, $publicPath);
        }

        return file_exists($publicPath) ? 'images/properties/' . $filename : $placeholder;
    }

    /**
     * Ensure multiple images exist in public folder
     */
    public function ensureMultipleImages(Property $property)
    {
        foreach ($property->images as $img) {
            $filename = basename($img->image_path);
            $storagePath = storage_path('app/public/properties/' . $filename);
            $publicPath = public_path('images/properties/' . $filename);

            if (!file_exists($publicPath) && file_exists($storagePath)) {
                copy($storagePath, $publicPath);
            }

            $img->update(['image_path' => 'images/properties/' . $filename]);
        }
    }

    /**
     * Store single uploaded image in storage & copy to public
     */
    protected function storeImage(UploadedFile $file): string
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('properties', $filename, 'public');

        $storagePath = storage_path('app/public/properties/' . $filename);
        $publicPath = public_path('images/properties/' . $filename);

        if (!file_exists($publicPath)) {
            copy($storagePath, $publicPath);
        }

        return 'images/properties/' . $filename;
       
    }

    /**
     * Store multiple uploaded images
     */
    protected function storeMultipleImages(Property $property, array $files): void
    {
        foreach ($files as $file) {
            $path = $this->storeImage($file);
            PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => $path,
            ]);
        }
    }

    /**
     * Delete a file from storage
     */
    protected function deleteFile(?string $path): void
    {
        if ($path) {
            $diskPath = str_replace('images/properties/', '', $path);
            if (Storage::disk('public')->exists('properties/' . $diskPath)) {
                Storage::disk('public')->delete('properties/' . $diskPath);
            }

            $publicPath = public_path($path);
            if (file_exists($publicPath)) {
                unlink($publicPath);
            }
        }
    }

    /**
     * Search & filter properties
     */
    public function search(array $filters)
    {
        $query = Property::query();

        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        if (!empty($filters['search_term'])) {
            $term = $filters['search_term'];
            $query->where(function ($q) use ($term) {
                $q->where('category', 'LIKE', "%$term%")
                  ->orWhere('location', 'LIKE', "%$term%");
            });
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $sort = $filters['sort_by'] ?? 'id DESC';
        [$field, $direction] = explode(' ', $sort);
        $query->orderBy($field, $direction);

        return $query->with('images')->get();
    }
}

