<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an optional moderation note to an ad when an admin rejects it.
 * Stored so the seller can see why the listing was turned down.
 */
return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('product')) return;
        if (Schema::hasColumn('product', 'reject_reason')) return;
        Schema::table('product', function (Blueprint $t) {
            $t->text('reject_reason')->nullable()->after('admin_seen');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('product') && Schema::hasColumn('product', 'reject_reason')) {
            Schema::table('product', function (Blueprint $t) {
                $t->dropColumn('reject_reason');
            });
        }
    }
};
