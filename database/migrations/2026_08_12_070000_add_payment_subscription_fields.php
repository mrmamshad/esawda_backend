<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add subscription tracking to users and payment linking to ads.
     */
    public function up(): void
    {
        // Add subscription fields to user table
        Schema::table('user', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_id')->nullable()->after('user_type');
            $table->timestamp('plan_expires_at')->nullable()->after('plan_id');
            $table->integer('ads_remaining')->default(0)->after('plan_expires_at');

            $table->index('plan_id');
            $table->index(['plan_id', 'plan_expires_at']);
        });

        // Add payment tracking fields to product (ad) table
        Schema::table('product', function (Blueprint $table) {
            $table->boolean('paid')->default(false)->after('status');
            $table->unsignedBigInteger('transaction_id')->nullable()->after('paid');

            $table->index(['user_id', 'paid']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropIndex(['user_plan_id_index']);
            $table->dropIndex(['user_plan_id_plan_expires_at_index']);
            $table->dropColumn(['plan_id', 'plan_expires_at', 'ads_remaining']);
        });

        Schema::table('product', function (Blueprint $table) {
            $table->dropIndex(['product_user_id_paid_index']);
            $table->dropIndex(['product_transaction_id_index']);
            $table->dropColumn(['paid', 'transaction_id']);
        });
    }
};
