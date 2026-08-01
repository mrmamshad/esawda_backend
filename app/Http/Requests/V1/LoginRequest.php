<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // The `identifier` may be either email or username.
            'identifier' => ['required', 'string', 'max:191'],
            'password'   => ['required', 'string', 'min:1'],
            'device'     => ['nullable', 'string', 'max:60'],
        ];
    }
}
