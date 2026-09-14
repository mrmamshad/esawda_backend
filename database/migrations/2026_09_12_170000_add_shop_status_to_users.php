<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Decouple "shop is listed on the public Shops page" from "account may log
 * in".
 *
 * `user.status` ('0'/'1') gates LOGIN and is what the Users page Ban uses —
 * it must never be the shop-listing switch, or deactivating a shop (or the
 * new-shop default) locks the owner out. This adds a dedicated
 * `shop_status` ('active'/'inactive') that the Shops directory and the admin
 * Shops Activate/Deactivate toggle use instead.
 *
 * Recovery: shops created while the previous release (wrongly) set
 * status='0' on apply are stuck un-login-able. Those rows are restored to
 * status='1' (login works) and marked shop_status='inactive' (still hidden
 * from the public list until an admin activates them).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('user', 'shop_status')) {
            Schema::table('user', function (Blueprint $table): void {
                $table->enum('shop_status', ['active', 'inactive'])
                    ->default('active')
                    ->after('shop_verified_at');
            });
        }

        // Default 'active' keeps every existing shop listed exactly as before.
        DB::table('user')->where('user_type', 'seller')->update(['shop_status' => 'active']);

        // Un-stick the shops the old flow disabled: allow login again, but
        // hide from the directory (inactive) pending admin activation.
        DB::table('user')
            ->where('user_type', 'seller')
            ->where('status', '0')
            ->update(['status' => '1', 'shop_status' => 'inactive']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('user', 'shop_status')) {
            Schema::table('user', function (Blueprint $table): void {
                $table->dropColumn('shop_status');
            });
        }
    }
};
