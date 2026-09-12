<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Normalise legacy `shop_category` values saved from the product-category
 * dropdown (e.g. "Fashion") to the shop taxonomy the /shops sidebar and
 * filter use (e.g. "Fashion & Apparel").
 *
 * Only values that are NOT already valid shop categories are touched, and
 * only when the mapped target exists in the current taxonomy (so an admin
 * custom list is never fought against). One-way: mismatched rows would
 * otherwise sit under no sidebar entry with broken counts/filters.
 */
return new class extends Migration
{
    /** Product-taxonomy name (lowercased) => shop-taxonomy name. */
    private const ALIASES = [
        'fashion' => 'Fashion & Apparel',
        'mobiles & tablets' => 'Mobiles & Gadgets',
        'electronics & appliances' => 'Electronics',
        'beauty & personal care' => 'Health & Beauty',
        'food & beverage' => 'Groceries & Food',
        'cars & bikes' => 'Vehicles & Parts',
        'home & lifestyle' => 'Home & Living',
        'sports, hobbies & books' => 'Sports & Outdoors',
        'jobs' => 'Services',
        'hotels & tourism' => 'Services',
        'entertainment' => 'Services',
        'real estate' => 'Other',
    ];

    public function up(): void
    {
        $taxonomy = $this->currentTaxonomy();
        $valid = [];
        foreach ($taxonomy as $name) {
            $valid[mb_strtolower(trim($name))] = trim($name);
        }

        $rows = DB::table('user')
            ->whereNotNull('shop_category')
            ->where('shop_category', '<>', '')
            ->select('id', 'shop_category')
            ->get();

        foreach ($rows as $row) {
            $current = trim((string) $row->shop_category);
            if ($current === '' || isset($valid[mb_strtolower($current)])) {
                continue;
            }
            $target = self::ALIASES[mb_strtolower($current)] ?? null;
            if ($target === null || !isset($valid[mb_strtolower($target)])) {
                continue;
            }

            DB::table('user')->where('id', $row->id)->update([
                'shop_category' => $valid[mb_strtolower($target)],
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // One-way normalisation — original free-text values are not retained.
    }

    /** @return array<int, string> */
    private function currentTaxonomy(): array
    {
        try {
            $raw = DB::table('options')->where('option_name', 'shop_categories')->value('option_value');
        } catch (Throwable) {
            return [];
        }

        if (is_string($raw)) {
            $trimmed = trim($raw);
            $decoded = str_starts_with($trimmed, '[') ? json_decode($trimmed, true) : null;
            $values = is_array($decoded)
                ? $decoded
                : preg_split('/[\r\n,]+/', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            return [];
        }

        $out = [];
        foreach ($values ?: [] as $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $name = trim((string) $value);
            if ($name !== '') {
                $out[] = $name;
            }
        }

        return $out;
    }
};
