<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cash-on-delivery (COD) guest orders.
 *
 * The legacy "Buy Now" flow required a paid transaction and an account,
 * so `buyer_id` was a hard FK and the status set was payment-oriented.
 * "Place Order" captures buyer contact (name / phone / address) instead,
 * so orders can exist without a registered buyer and the seller manages
 * a simple Pending → Confirmed → Delivered / Cancelled lifecycle.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('buyer_name', 150)->nullable()->after('buyer_id');
            $table->string('buyer_phone', 20)->nullable()->after('buyer_name');
            $table->text('buyer_address')->nullable()->after('buyer_phone');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('buyer_id')->nullable()->change();
            // Old enum (pending, processing, shipped, delivered, cancelled) is
            // replaced by a plain string so both legacy rows and the new
            // 4-state lifecycle (pending, confirmed, delivered, cancelled) fit.
            $table->string('shipping_status', 20)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['buyer_name', 'buyer_phone', 'buyer_address']);
        });
    }
};
