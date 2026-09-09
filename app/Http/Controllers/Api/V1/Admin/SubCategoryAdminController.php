<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RevalidateFrontendJob;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/** Complete lifecycle management for the legacy product subcategories table. */
class SubCategoryAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = SubCategory::query()->orderBy('cat_order')->orderBy('sub_cat_name');
        if ($request->filled('category')) {
            $query->where('main_cat_id', (int) $request->query('category'));
        }

        return $this->ok($query->withCount('posts')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'main_cat_id' => ['required', 'integer', 'exists:catagory_main,cat_id'],
            'sub_cat_name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:150'],
            'cat_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['sub_cat_name']);
        $data['cat_order'] ??= ((int) SubCategory::where('main_cat_id', $data['main_cat_id'])->max('cat_order')) + 1;

        $sub = SubCategory::create($data);
        $this->bustCategoryCaches();

        return $this->created($sub->fresh()->loadCount('posts'));
    }

    public function update(int $id, Request $request)
    {
        $sub = SubCategory::findOrFail($id);

        $data = $request->validate([
            'main_cat_id' => ['sometimes', 'integer', 'exists:catagory_main,cat_id'],
            'sub_cat_name' => ['sometimes', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:150'],
            'cat_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        if (array_key_exists('slug', $data) && !$data['slug']) {
            $data['slug'] = Str::slug((string) ($data['sub_cat_name'] ?? $sub->sub_cat_name));
        }

        // Moving a subcategory to another parent is a taxonomy change —
        // moving WITH products would silently re-file live listings.
        if (array_key_exists('main_cat_id', $data)
            && (int) $data['main_cat_id'] !== (int) $sub->main_cat_id
            && $sub->posts()->exists()) {
            return $this->error(
                'SUBCATEGORY_IN_USE',
                'Move or delete this subcategory’s products before moving it to another category.',
                409,
            );
        }

        $sub->fill($data)->save();
        $this->bustCategoryCaches();

        return $this->ok($sub->fresh()->loadCount('posts'));
    }

    public function destroy(int $id)
    {
        $sub = SubCategory::findOrFail($id);
        if ($sub->posts()->exists()) {
            return $this->error(
                'SUBCATEGORY_IN_USE',
                'Move or delete this subcategory’s products first.',
                409,
            );
        }

        $sub->delete();
        $this->bustCategoryCaches();

        return $this->ok(['message' => 'Subcategory deleted.']);
    }

    /**
     * Same taxonomy caches as the main category endpoints — a subcategory
     * write changes the same public payloads, so drop them all and poke
     * the frontend to regenerate.
     */
    private function bustCategoryCaches(): void
    {
        foreach ([0, 1] as $counts) {
            foreach ([0, 1] as $subs) {
                Cache::forget("categories.{$counts}.{$subs}");
            }
        }
        Cache::forget('home.payload');
        RevalidateFrontendJob::dispatch();
    }
}
