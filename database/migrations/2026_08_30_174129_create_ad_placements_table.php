<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modern ad management table — replaces the legacy `adsense` track-code
     * storage with image-based, placement-targeted ads that admins can
     * upload per slot and schedule with an expiry date.
     *
     * `slug` = the placement id the frontend AdSlot uses
     *          (e.g. "home.after_categories", "store.sidebar").
     */
    public function up(): void
    {
        Schema::create('ad_placements', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title')->nullable();
            $table->string('size')->default('wide');
            $table->string('image_path')->nullable();
            $table->string('link_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_placements');
    }
};
