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
    public function updateProperty(Property $property, array $data, ?UploadedFile $image = null, ?array $multipleImages = null): Property
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
        // $filename = time() . '_' . $file->getClientOriginalName();
        // $path = $file->storeAs('properties', $filename, 'public');

        // $storagePath = storage_path('app/public/properties/' . $filename);
        // $publicPath = public_path('images/properties/' . $filename);

        // if (!file_exists($publicPath)) {
        //     copy($storagePath, $publicPath);
        // }

        // return 'images/properties/' . $filename;
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('properties', $filename, 'public'); // storage/app/public/properties
        return $filename; 
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

