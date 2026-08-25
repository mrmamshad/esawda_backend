<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Category CRUD. Maps to the legacy `catagory_main` table which uses
 * `cat_id` PK plus `cat_name`, `slug`, `icon`, `picture`, `cat_order`.
 */
class CategoryAdminController extends Controller
{
    public function index()
    {
        return $this->ok(Category::orderBy('cat_name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cat_name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'picture' => ['nullable', 'string'],
            'cat_order' => ['nullable', 'integer'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['cat_name']);
        $data['icon'] = $data['icon'] ?? 'fa-usd';

        return $this->created(Category::create($data));
    }

    public function show(int $id)
    {
        return $this->ok(Category::findOrFail($id));
    }

    public function update(int $id, Request $request)
    {
        $c = Category::findOrFail($id);
        $data = $request->validate([
            'cat_name' => ['sometimes', 'string', 'max:100'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:100'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:100'],
            'picture' => ['sometimes', 'nullable', 'string'],
            'cat_order' => ['sometimes', 'integer'],
        ]);
        $c->fill($data)->save();

        return $this->ok($c);
    }

    public function destroy(int $id)
    {
        Category::findOrFail($id)->delete();

        return $this->ok(['message' => 'Category deleted.']);
    }
}
