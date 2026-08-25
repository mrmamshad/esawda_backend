<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $uid = $this->user()->id;

        return [
            'name' => ['sometimes', 'string', 'max:120'],
            'email' => ['sometimes', 'email', 'max:190', Rule::unique('user', 'email')->ignore($uid)],
            'phone' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'url', 'max:190'],
            'facebook' => ['nullable', 'string', 'max:190'],
            'twitter' => ['nullable', 'string', 'max:190'],
            'instagram' => ['nullable', 'string', 'max:190'],
            'linkedin' => ['nullable', 'string', 'max:190'],
            'youtube' => ['nullable', 'string', 'max:190'],
        ];
    }
}
