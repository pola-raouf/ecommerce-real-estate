<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $property = $this->route('property');
        // Only owners or admins can update
        return auth()->user()->can('update', $property);
    }

    public function rules()
{
    return [
        'category' => 'required|string',
        'location' => 'required|string',
        'price' => 'required|numeric|min:0',
        'status' => 'required|string|in:available,pending,sold',
        'description' => 'nullable|string',
        'transaction_type' => 'required|string|in:sale,rent',
        'installment_years' => 'nullable|numeric|min:0',
        'user_id' => 'nullable|exists:users,id',
        'image' => 'nullable|image|mimes:jpg,png,jpeg|max:4096',
        'multiple_images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:4096',
    ];
}

}

