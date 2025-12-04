<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adjust if needed for roles/permissions
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        return [
            'name' => 'required|string|max:30',
            'email' => [
                'required',
                'email',
                'max:60',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'required|numeric|digits_between:10,11',
            'birth_date' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:male,female,other',
            'location' => 'required|string|max:255',
            'role' => 'required|in:admin,seller,buyer',
        ];
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'Passwords do not match.',
            'birth_date.before_or_equal' => 'Birth date cannot be in the future.',
        ];
    }
}

