<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\SubCategoryResource;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
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
        $withSubs   = filter_var($request->query('with_subs',   'true'), FILTER_VALIDATE_BOOL);

        $query = Category::orderBy('cat_order');

        if ($withSubs) {
            $query->with(['subCategories' => function ($q) use ($withCounts) {
                $q->orderBy('cat_order');
                if ($withCounts) $q->select('*')->selectSub(
                    DB::table('product')
                      ->selectRaw('count(*)')
                      ->whereColumn('sub_category', 'catagory_sub.sub_cat_id')
                      ->where('status', 'active')->where('hide', '0'),
                    'ads_count'
                );
            }]);
        }

        if ($withCounts) {
            $query->selectSub(
                DB::table('product')
                  ->selectRaw('count(*)')
                  ->whereColumn('category', 'catagory_main.cat_id')
                  ->where('status', 'active')->where('hide', '0'),
                'ads_count'
            )->addSelect('catagory_main.*');
        }

        return $this->ok(CategoryResource::collection($query->get()));
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
