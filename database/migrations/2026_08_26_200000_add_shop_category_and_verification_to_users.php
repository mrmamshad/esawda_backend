<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table): void {
            $table->string('shop_category', 100)->nullable()->after('shop_name');
            $table->timestamp('shop_verified_at')->nullable()->after('shop_documents');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table): void {
            $table->dropColumn(['shop_category', 'shop_verified_at']);
        });
    }
};
