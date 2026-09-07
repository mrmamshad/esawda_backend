<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table): void {
            $table->index(
                ['user_type', 'status', 'shop_category'],
                'user_shop_directory_filter_idx',
            );
            $table->index(
                ['user_type', 'status', 'shop_verified_at', 'created_at'],
                'user_shop_directory_sort_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table): void {
            $table->dropIndex('user_shop_directory_filter_idx');
            $table->dropIndex('user_shop_directory_sort_idx');
        });
    }
};
