<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->json('bundle_items')->nullable()->after('whatsapp');
        });
        Schema::table('user', function (Blueprint $table) {
            $table->string('cover', 225)->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('bundle_items');
        });
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('cover');
        });
    }
};
