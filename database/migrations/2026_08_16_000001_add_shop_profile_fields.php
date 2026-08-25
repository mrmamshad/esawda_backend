<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Shop-owner onboarding fields. `user_type` enum already has a
     * 'seller' value; these columns carry the shop profile the owner
     * submits when opening their shop (Bikroy-style application) plus
     * the uploaded identity/business documents.
     */
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('shop_name', 191)->nullable()->after('ads_remaining');
            $table->text('shop_description')->nullable()->after('shop_name');
            $table->string('shop_address', 500)->nullable()->after('shop_description');
            $table->json('shop_documents')->nullable()->after('shop_address');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn(['shop_name', 'shop_description', 'shop_address', 'shop_documents']);
        });
    }
};
