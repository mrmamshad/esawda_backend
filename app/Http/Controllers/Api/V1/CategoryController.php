<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * /api/v1/categories, /api/v1/categories/{slug}, /api/v1/subcategories
 *
 * Only public reads — no admin write ops here (Filament handles that).
 * Active-ad counts are joined on demand so sidebar badges match the
 * Browse-page reference (e.g. "Bikes (68, 043)").
 */
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $withCounts = filter_var($request->query('with_counts', 'true'), FILTER_VALIDATE_BOOL);
        $withSubs = filter_var($request->query('with_subs', 'true'), FILTER_VALIDATE_BOOL);

        // Read-heavy public taxonomy. The per-row correlated COUNT subqueries
        // become a handful of grouped COUNT()s, and the whole result is cached
        // for 5 minutes via the configured CACHE_STORE (file in dev, redis in
        // prod) — not a DB hit per request.
        $key = 'categories.'.(int) $withCounts.'.'.(int) $withSubs;

        return Cache::remember($key, 300, function () use ($withCounts, $withSubs) {
            $query = Category::orderBy('cat_order');

            if ($withSubs) {
                $query->with(['subCategories' => function ($q) use ($withCounts) {
                    $q->orderBy('cat_order');
                    // group-by COUNT per category, aliased to the ads_count the
                    // resource expects (replaces the per-row correlated subquery)
                    if ($withCounts) {
                        $q->withCount(['posts as ads_count' => fn ($a) => $a->active()]);
                    }
                }]);
            }

            if ($withCounts) {
                $query->withCount([
                    'posts as ads_count' => fn ($a) => $a->active(),
                    // Per-condition totals power the homepage Used/New toggle.
                    'posts as new_count' => fn ($a) => $a->active()->where('condition', 'new'),
                    'posts as used_count' => fn ($a) => $a->active()->where('condition', 'used'),
                ]);
            }

            return CategoryResource::collection($query->get());
        });
    }

    public function show(string $slug)
    {
        $cat = Category::where('slug', $slug)
            ->orWhere('cat_id', is_numeric($slug) ? (int) $slug : -1)
            ->with(['subCategories' => fn ($q) => $q->orderBy('cat_order')])
            ->firstOrFail();

        return $this->ok(new CategoryResource($cat));
    }
}
