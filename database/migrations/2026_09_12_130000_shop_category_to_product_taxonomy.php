<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Point stored `shop_category` values at the PRODUCT taxonomy, which is
 * now the single source of truth (ShopCategoryService reads
 * `catagory_main`).
 *
 * Values already matching a product category (case-insensitive) are
 * canonicalised to its exact name; legacy shop-taxonomy names are mapped
 * across (Fashion & Apparel → Fashion, …) only when the target product
 * category actually exists. Anything unrecognised is left untouched.
 */
return new class extends Migration
{
    /** Legacy shop-taxonomy name (lowercased) => product category name. */
    private const ALIASES = [
        'fashion & apparel' => 'Fashion',
        'mobiles & gadgets' => 'Mobiles & Tablets',
        'electronics' => 'Electronics & Appliances',
        'health & beauty' => 'Beauty & Personal Care',
        'groceries & food' => 'Food & Beverage',
        'vehicles & parts' => 'Cars & Bikes',
        'home & living' => 'Home & Living',
        'home & lifestyle' => 'Home & Living',
        'sports & outdoors' => 'Sports, Hobbies & Books',
        'books & stationery' => 'Sports, Hobbies & Books',
        'services' => 'Services',
        'jobs' => 'Jobs',
        'hotels & tourism' => 'Hotels & Tourism',
        'entertainment' => 'Entertainment',
        'real estate' => 'Real Estate',
        'food & beverage' => 'Food & Beverage',
        'cars & bikes' => 'Cars & Bikes',
        'mobiles & tablets' => 'Mobiles & Tablets',
        'electronics & appliances' => 'Electronics & Appliances',
        'beauty & personal care' => 'Beauty & Personal Care',
    ];

    public function up(): void
    {
        try {
            $products = DB::table('catagory_main')
                ->orderBy('cat_order')
                ->pluck('cat_name');
        } catch (Throwable) {
            return;
        }

        $valid = [];
        foreach ($products as $name) {
            $name = trim((string) $name);
            if ($name !== '') {
                $valid[mb_strtolower($name)] = $name;
            }
        }
        if (!$valid) {
            return;
        }

        try {
            $rows = DB::table('user')
                ->whereNotNull('shop_category')
                ->where('shop_category', '<>', '')
                ->select('id', 'shop_category')
                ->get();
        } catch (Throwable) {
            return;
        }

        foreach ($rows as $row) {
            $current = trim((string) $row->shop_category);
            if ($current === '') {
                continue;
            }
            $key = mb_strtolower($current);

            if (isset($valid[$key])) {
                $target = $valid[$key];
            } else {
                $alias = self::ALIASES[$key] ?? null;
                if ($alias === null || !isset($valid[mb_strtolower($alias)])) {
                    continue;
                }
                $target = $valid[mb_strtolower($alias)];
            }

            if ($target !== $row->shop_category) {
                DB::table('user')->where('id', $row->id)->update([
                    'shop_category' => $target,
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // One-way normalisation — previous free-text values are not retained.
    }
};
