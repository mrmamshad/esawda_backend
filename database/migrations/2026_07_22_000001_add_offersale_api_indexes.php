<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Non-destructive performance indexes for the offersale. API rollout.
 *
 * Adds composite indexes that the /api/v1/ads browse + chat threads
 * queries rely on. No columns added or removed; rollback drops the
 * indexes only. Safe on existing production data.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product')) {
            Schema::table('product', function (Blueprint $t) {
                // Browse: active ads filtered by category / city / country.
                $t->index(['status', 'hide', 'category'], 'idx_product_browse_cat');
                $t->index(['status', 'hide', 'city'], 'idx_product_browse_city');
                $t->index(['status', 'hide', 'country'], 'idx_product_browse_country');
                // Featured / promo rails (ORDER BY featured DESC, id DESC).
                $t->index(['featured', 'urgent', 'highlight'], 'idx_product_promo');
                // Owner dashboard scans by user.
                $t->index(['user_id', 'status'], 'idx_product_user_status');
            });
        }

        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $t) {
                $t->index(['to_id', 'seen'], 'idx_messages_inbox_unseen');
                $t->index(['from_id', 'to_id', 'message_date'], 'idx_messages_thread');
            });
        }

        if (Schema::hasTable('favads')) {
            Schema::table('favads', function (Blueprint $t) {
                $t->index(['user_id', 'product_id'], 'idx_favads_user_product');
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $t) {
                $t->index(['productID', 'publish'], 'idx_reviews_product_publish');
            });
        }
    }

    public function down(): void
    {
        $drop = function (string $table, array $indexes): void {
            if (!Schema::hasTable($table)) {
                return;
            }
            Schema::table($table, function (Blueprint $t) use ($indexes) {
                foreach ($indexes as $ix) {
                    try {
                        $t->dropIndex($ix);
                    } catch (Throwable $e) { /* ignore */
                    }
                }
            });
        };
        $drop('product', [
            'idx_product_browse_cat', 'idx_product_browse_city',
            'idx_product_browse_country', 'idx_product_promo',
            'idx_product_user_status',
        ]);
        $drop('messages', ['idx_messages_inbox_unseen', 'idx_messages_thread']);
        $drop('favads', ['idx_favads_user_product']);
        $drop('reviews', ['idx_reviews_product_publish']);
    }
};
