<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class GuestRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:225'],
            'mobile' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'device' => ['nullable', 'string', 'max:60'],
        ];
    }
}
