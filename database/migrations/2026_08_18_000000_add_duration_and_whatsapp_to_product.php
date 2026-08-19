<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->integer('duration_days')->nullable()->default(30)->after('expire_date');
            $table->string('whatsapp', 50)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn(['duration_days', 'whatsapp']);
        });
    }
};
