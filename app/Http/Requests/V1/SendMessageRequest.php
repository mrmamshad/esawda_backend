<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'to'      => ['required', 'integer', 'exists:user,id'],
            'body'    => ['required_without:image', 'string', 'min:1', 'max:5000'],
            'image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'],
            'type'    => ['nullable', 'string', 'in:text,image,offer,payment_request'],
            'post_id' => ['nullable', 'integer', 'exists:product,id'],
        ];
    }
}
