<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_events', function (Blueprint $table) {
            $table->id();
            // Unprefixed table name — the connection's `ad_` prefix is applied
            // automatically, resolving to `ad_licenses`.
            $table->foreignId('license_id')->constrained('licenses')->cascadeOnDelete();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('downloaded_at')->useCurrent();

            $table->index(['license_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_events');
    }
};
