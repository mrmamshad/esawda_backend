<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

/**
 * Single source of truth for shop categories: the PRODUCT taxonomy
 * (`catagory_main`, managed from Admin → Categories).
 *
 * Shops used to have their own list (`shop_categories` option), but the
 * names never matched the product categories, so sidebar counts and
 * filters missed shops. Now onboarding, validation, the /shops sidebar
 * and the filter all resolve against this one list.
 */
class ShopCategoryService
{
    /** @return array<int, string> */
    public function all(): array
    {
        try {
            return Category::orderBy('cat_order')
                ->orderBy('cat_name')
                ->pluck('cat_name')
                ->map(fn ($name) => trim((string) $name))
                ->filter()
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** Resolve either a category name or its URL-safe slug. */
    public function resolve(?string $value): ?string
    {
        $needle = trim((string) $value);
        if ($needle === '') {
            return null;
        }

        foreach ($this->all() as $category) {
            if (strcasecmp($category, $needle) === 0 || Str::slug($category) === Str::slug($needle)) {
                return $category;
            }
        }

        return null;
    }
}
