<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $property = $this->route('property');
        $user = auth()->user();
        
        // Admins can update any property, sellers can only update their own
        return $user && ($user->role === 'admin' || $property->user_id === $user->id);
    }

    public function rules(): array
    {
        return [
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pending,available,sold,reserved',
            'description' => 'nullable|string',
            'transaction_type' => 'required|string|in:sale,rent',
            'installment_years' => 'nullable|integer|min:0',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'user_id' => 'nullable|exists:users,id',
            'multiple_images' => 'nullable|array',
            'multiple_images.*' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'nullable|integer|exists:property_images,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Remove empty user_id to avoid validation errors
        if ($this->has('user_id') && empty($this->user_id)) {
            $this->request->remove('user_id');
        }
    }

}

