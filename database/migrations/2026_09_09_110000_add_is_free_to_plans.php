<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Zero-price plans (Early Bird promos) are activated without a gateway.
     * `is_free` keeps that decision explicit instead of string-checking the
     * price everywhere.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_free')->default(0)->after('lifetime_price');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
    }
};
