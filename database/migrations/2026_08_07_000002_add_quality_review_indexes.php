<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Non-destructive coverage for query gaps flagged in the quality review:
 *   - custom_data.product_id/field_id   (filter joins + sync wipes)
 *   - messages.post_id                 (ad threads)
 *   - product.sub_category            (browse / similar)
 *   - reviews.user_id                 (seller aggregate)
 * No columns added/dropped; rollback drops only the indexes.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('custom_data')) {
            Schema::table('custom_data', function (Blueprint $t) {
                $t->index(['product_id', 'field_id'], 'idx_custom_data_product_field');
            });
        }
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $t) {
                $t->index(['post_id'], 'idx_messages_post');
            });
        }
        if (Schema::hasTable('product')) {
            Schema::table('product', function (Blueprint $t) {
                $t->index(['sub_category'], 'idx_product_subcategory');
            });
        }
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $t) {
                $t->index(['user_id'], 'idx_reviews_user');
            });
        }
    }

    public function down(): void
    {
        $drop = function (string $table, array $indexes): void {
            if (! Schema::hasTable($table)) return;
            Schema::table($table, function (Blueprint $t) use ($indexes) {
                foreach ($indexes as $ix) {
                    try { $t->dropIndex($ix); } catch (\Throwable $e) { /* ignore */ }
                }
            });
        };
        $drop('custom_data', ['idx_custom_data_product_field']);
        $drop('messages',    ['idx_messages_post']);
        $drop('product',     ['idx_product_subcategory']);
        $drop('reviews',     ['idx_reviews_user']);
    }
};
