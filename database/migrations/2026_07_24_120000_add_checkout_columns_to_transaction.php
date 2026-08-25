<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds SPA-checkout columns to the legacy `ad_transaction` table:
 *   - plan_id     : nullable FK-style reference to the purchased plan
 *   - purpose     : free-text ('plan' | 'ad_upgrade' | …) so admin can filter
 *   - meta        : JSON blob capturing per-purpose extras (upgrade flags, etc)
 *   - created_at / updated_at : make the row queryable by admin dashboards
 *
 * These live alongside the legacy columns (transaction_time, base_amount,
 * featured/urgent/highlight flags, billing) which stay untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $t) {
            if (!Schema::hasColumn('transaction', 'plan_id')) {
                $t->unsignedInteger('plan_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('transaction', 'purpose')) {
                $t->string('purpose', 40)->nullable()->after('status');
            }
            if (!Schema::hasColumn('transaction', 'meta')) {
                $t->text('meta')->nullable()->after('purpose');
            }
            if (!Schema::hasColumn('transaction', 'created_at')) {
                $t->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('transaction', 'updated_at')) {
                $t->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $t) {
            foreach (['plan_id', 'purpose', 'meta', 'created_at', 'updated_at'] as $c) {
                if (Schema::hasColumn('transaction', $c)) {
                    $t->dropColumn($c);
                }
            }
        });
    }
};
