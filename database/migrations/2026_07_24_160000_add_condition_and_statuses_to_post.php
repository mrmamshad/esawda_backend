<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds product condition + expanded status vocabulary to the legacy
 * `ad_product` table.
 *
 *   condition : 'new' | 'used'  → whether the item is brand new or second-hand
 *
 * Also expands the `status` ENUM to accept the new lifecycle states used
 * by the Shop dashboard sidebar (draft, sold_out, removed alongside the
 * existing pending/active/rejected/expire).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Skip entirely if the legacy product table isn't present (e.g.
        // fresh SQLite test databases that don't run the legacy seed).
        if (!Schema::hasTable('product')) {
            return;
        }

        // 1. Add condition column with index — use Schema builder so it
        //    works on MySQL, MariaDB, and SQLite alike.
        if (!Schema::hasColumn('product', 'condition')) {
            Schema::table('product', function (Blueprint $t) {
                $t->string('condition', 10)->default('used')->index();
            });
        }

        // 2. Expand `status` ENUM to accept the new lifecycle states.
        //    Only valid on MySQL/MariaDB — silently no-op elsewhere.
        try {
            DB::statement("ALTER TABLE ad_product MODIFY COLUMN status ENUM('draft','pending','active','rejected','sold_out','removed','expire') NOT NULL DEFAULT 'pending'");
        } catch (Throwable $e) { /* SQLite etc. */
        }

        // 3. Fast status filtering on the shop dashboard.
        try {
            DB::statement('CREATE INDEX product_status_user_idx ON ad_product (status, user_id)');
        } catch (Throwable $e) {
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product', 'condition')) {
            DB::statement('ALTER TABLE ad_product DROP COLUMN `condition`');
        }
        try {
            DB::statement('DROP INDEX product_status_user_idx ON ad_product');
        } catch (Throwable $e) {
        }
    }
};
