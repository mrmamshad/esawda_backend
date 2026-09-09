<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Username is optional — the signup form collects a mobile
            // number instead and the controller auto-generates a unique
            // username from it (same as the guest flow).
            'username' => ['nullable', 'string', 'min:3', 'max:40',
                'regex:/^[A-Za-z0-9_.-]+$/',
                Rule::unique('user', 'username')],
            'email' => ['required', 'email:rfc', 'max:191',
                Rule::unique('user', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'name' => ['nullable', 'string', 'max:225'],
            'phone' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username may only contain letters, digits, dot, dash, underscore.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone.regex' => 'Enter a valid 11-digit Bangladeshi mobile number, e.g. 01712345678.',
        ];
    }
}
