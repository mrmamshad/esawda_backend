<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'description' => ['required', 'string', 'min:10'],
            'condition' => ['required', 'in:new,used'],
            'category' => ['required', 'integer', 'exists:catagory_main,cat_id'],
            'sub_category' => ['nullable', 'integer', 'exists:catagory_sub,sub_cat_id'],
            'price' => ['required', 'integer', 'min:0'],
            'negotiable' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'duration_days' => ['nullable', 'integer', Rule::in([7, 30, 90])],
            'hide_phone' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:50'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'custom' => ['nullable', 'array'],   // { field_id: value }
            'bundle_items' => ['nullable', 'array', 'min:1', 'max:20'],
            'bundle_items.*' => ['integer'],
        ];
    }
}
