<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Default shop-category list for the "Open your shop" dropdown.
 *
 * Stored as a JSON array in the legacy `option` table (key
 * `shop_categories`) so admins can edit it from the existing
 * Settings UI (PUT /api/v1/admin/settings) with no code change —
 * comma-separated or newline-separated text works too, the frontend
 * parses all three shapes. Publicly readable via GET /api/v1/settings.
 */
return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('options')->where('option_name', 'shop_categories')->exists();
        if ($exists) {
            return;
        }

        DB::table('options')->insert([
            'option_name' => 'shop_categories',
            'option_value' => json_encode([
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
            ]),
        ]);
    }

    public function down(): void
    {
        DB::table('options')->where('option_name', 'shop_categories')->delete();
    }
};
