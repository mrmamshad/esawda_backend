<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\FilterFieldResource;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * GET /api/v1/filter-schema
 *
 *   ?category=vehicles           (slug or numeric id)
 *   ?sub_category=cars
 *
 * Returns the ordered list of "Advance Filter" fields that apply to
 * the requested category. Sourced from the legacy `custom_fields`
 * table where `custom_anycat`/`custom_catid`/`custom_subcatid` control
 * scoping:
 *
 *   custom_anycat = '1'           → applies to all categories
 *   custom_catid  = "1|3|4"       → pipe-separated main cat ids
 *   custom_subcatid = "10|11"     → pipe-separated sub cat ids
 *
 * If no `?category` is passed, only the "any" fields are returned.
 */
class FilterSchemaController extends Controller
{
    public function show(Request $request)
    {
        $catId    = $this->resolveCategoryId($request->query('category'));
        $subCatId = $this->resolveSubCategoryId($request->query('sub_category'));

        // Custom-field schema is static config; cache the resolved field list
        // by the (category, sub_category) scope. Resolved ids are null-safe.
        $cacheKey = 'filter-schema.' . ($catId ?? 'all') . '.' . ($subCatId ?? 'all');
        $fields = Cache::remember($cacheKey, 300, function () use ($catId, $subCatId) {
            return CustomField::query()
                ->orderBy('custom_order')
                ->get()
                ->filter(function (CustomField $f) use ($catId, $subCatId) {
                    if ($this->bool($f->custom_anycat)) return true;

                    if ($catId !== null && $this->pipeContains($f->custom_catid, $catId)) return true;
                    if ($subCatId !== null && $this->pipeContains($f->custom_subcatid, $subCatId)) return true;

                    return false;
                })
                ->values();
        });

        return $this->ok([
            'category'     => $catId,
            'sub_category' => $subCatId,
            'fields'       => FilterFieldResource::collection($fields)->resolve(),
        ]);
    }

    private function resolveCategoryId(mixed $raw): ?int
    {
        if ($raw === null || $raw === '') return null;
        if (is_numeric($raw)) return (int) $raw;
        return Category::where('slug', $raw)->value('cat_id');
    }

    private function resolveSubCategoryId(mixed $raw): ?int
    {
        if ($raw === null || $raw === '') return null;
        if (is_numeric($raw)) return (int) $raw;
        return SubCategory::where('slug', $raw)->value('sub_cat_id');
    }

    private function pipeContains(?string $raw, int $needle): bool
    {
        if (empty($raw)) return false;
        $parts = array_map('intval', array_filter(preg_split('/[|,]/', $raw)));
        return in_array($needle, $parts, true);
    }

    private function bool($v): bool
    {
        return in_array($v, [1, '1', true, 'true'], true);
    }
}
