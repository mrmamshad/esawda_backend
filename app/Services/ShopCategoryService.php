<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Str;

/**
 * Single source of truth for the admin-configurable shop taxonomy.
 *
 * The legacy options table may contain JSON, comma-separated, or newline-
 * separated values. Normalising it here keeps onboarding and public browsing
 * on the same category list without coupling shops to product categories.
 */
class ShopCategoryService
{
    private const DEFAULT_CATEGORIES = [
        'Electronics',
        'Fashion & Apparel',
        'Groceries & Food',
        'Health & Beauty',
        'Home & Living',
        'Mobiles & Gadgets',
        'Vehicles & Parts',
        'Baby & Kids',
        'Sports & Outdoors',
        'Books & Stationery',
        'Services',
        'Other',
    ];

    /** @return array<int, string> */
    public function all(): array
    {
        try {
            $raw = Option::query()
                ->where('option_name', 'shop_categories')
                ->value('option_value');
        } catch (\Throwable) {
            return self::DEFAULT_CATEGORIES;
        }

        $categories = $this->parse($raw);

        return $categories ?: self::DEFAULT_CATEGORIES;
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

    /** @return array<int, string> */
    private function parse(mixed $raw): array
    {
        if (is_array($raw)) {
            $values = $raw;
        } elseif (is_string($raw)) {
            $trimmed = trim($raw);
            $decoded = str_starts_with($trimmed, '[') ? json_decode($trimmed, true) : null;
            $values = is_array($decoded)
                ? $decoded
                : preg_split('/[\r\n,]+/', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            return [];
        }

        $normalised = [];
        foreach ($values ?: [] as $value) {
            if (!is_scalar($value)) {
                continue;
            }

            $category = trim((string) $value);
            if ($category === '' || mb_strlen($category) > 100) {
                continue;
            }

            $normalised[mb_strtolower($category)] = $category;
        }

        return array_values($normalised);
    }
}
