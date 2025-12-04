<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can create properties
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,available,sold,reserved',
            'description' => 'required|string',
            'transaction_type' => 'required|string|in:sale,rent',
            'installment_years' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'user_id' => 'nullable|exists:users,id',
            'multiple_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }
}

