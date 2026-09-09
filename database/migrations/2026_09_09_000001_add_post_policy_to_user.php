<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-account posting override for the admin panel:
     *   inherit — follow the global posting rules (default)
     *   free    — post without a subscription, quota untouched
     *   blocked — cannot post at all (free or paid)
     */
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('post_policy', 16)->default('inherit')->after('ads_remaining');
            $table->index('post_policy');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropIndex(['post_policy']);
            $table->dropColumn('post_policy');
        });
    }
};
