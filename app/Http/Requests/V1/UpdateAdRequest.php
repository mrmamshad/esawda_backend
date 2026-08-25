<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // Every field is optional on update (PATCH-style) but must still
        // pass its own type/length checks when present.
        return [
            'title' => ['sometimes', 'string', 'min:3', 'max:150'],
            'description' => ['sometimes', 'string', 'min:10'],
            'condition' => ['sometimes', 'in:new,used'],
            'category' => ['sometimes', 'integer', 'exists:catagory_main,cat_id'],
            'sub_category' => ['sometimes', 'nullable', 'integer', 'exists:catagory_sub,sub_cat_id'],
            'price' => ['sometimes', 'integer', 'min:0'],
            'negotiable' => ['sometimes', 'boolean'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:50'],
            'duration_days' => ['sometimes', 'integer', Rule::in([7, 30, 90])],
            'bundle_items' => ['sometimes', 'array', 'min:1', 'max:20'],
            'bundle_items.*' => ['integer'],
            'hide_phone' => ['sometimes', 'boolean'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'city' => ['sometimes', 'nullable', 'string', 'max:50'],
            'state' => ['sometimes', 'nullable', 'string', 'max:50'],
            'country' => ['sometimes', 'nullable', 'string', 'max:50'],
            'lat' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'lng' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'images' => ['sometimes', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'custom' => ['sometimes', 'array'],
        ];
    }
}
