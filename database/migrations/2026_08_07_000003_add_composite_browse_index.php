<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Composite index for the default AdController::index sort:
 *     WHERE status='active' AND hide='0' ... ORDER BY featured DESC,
 *     urgent DESC, id DESC
 * Every column sorts DESC, so MySQL can satisfy it with a backwards scan
 * over this ascending index — replacing a filesort. Include `category` so
 * per-category browse (filter[category]=X) also skips the filesort.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product')) {
            return;
        }
        Schema::table('product', function (Blueprint $t) {
            $t->index(
                ['status', 'hide', 'category', 'featured', 'urgent', 'id'],
                'idx_product_browse_sort'
            );
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('product')) {
            return;
        }
        Schema::table('product', function (Blueprint $t) {
            try {
                $t->dropIndex('idx_product_browse_sort');
            } catch (Throwable $e) { /* ignore */
            }
        });
    }
};
