<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class ReservePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can reserve properties
        return auth()->check();
    }

    public function rules(): array
    {
        $property = $this->route('property');
        
        // Base rules for all reservations
        $rules = [
            'meeting_datetime' => 'required|date|after:now',
            'notes' => 'nullable|string|max:500',
        ];

        // Additional rules for rental properties
        if ($property && $property->transaction_type === 'rent') {
            $rules['start_date'] = 'required|date|after:meeting_datetime';
            $rules['duration_value'] = 'required|integer|min:1|max:100';
            $rules['duration_unit'] = 'required|in:weeks,months,years';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'meeting_datetime.required' => 'Please select a meeting date.',
            'meeting_datetime.after' => 'Meeting must be scheduled for a future date.',
            'start_date.required' => 'Please select a rental start date.',
            'start_date.after' => 'Rental start date must be after the meeting date.',
            'duration_value.required' => 'Please specify the rental duration.',
            'duration_value.min' => 'Duration must be at least 1.',
            'duration_value.max' => 'Duration cannot exceed 100.',
            'duration_unit.required' => 'Please select a duration unit (weeks, months, or years).',
        ];
    }
}
