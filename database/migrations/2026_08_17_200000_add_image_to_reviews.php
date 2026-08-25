<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reviews') && !Schema::hasColumn('reviews', 'image')) {
            Schema::table('reviews', function (Blueprint $t) {
                $t->string('image')->nullable()->after('comments');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'image')) {
            Schema::table('reviews', function (Blueprint $t) {
                $t->dropColumn('image');
            });
        }
    }
};
