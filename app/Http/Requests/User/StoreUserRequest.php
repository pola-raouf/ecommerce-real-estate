<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow all for now, you can add permissions later
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30',
            'email' => 'required|email|unique:users,email|max:60',
            'password' => 'required|string|min:8|',
            'phone' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'birth_date' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:male,female',
            'location' => 'required|string|max:255',
            'role' => 'required|in:admin,seller,buyer',
        ];
    }

    public function messages(): array
    {
        return [
            'birth_date.before_or_equal' => 'Birth date cannot be in the future.',
        ];
    }
}

