<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'token'    => ['required', 'string', 'min:20', 'max:120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
