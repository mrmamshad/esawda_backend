<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();          // ESW-XXXX-XXXX-XXXX
            $table->string('buyer_name')->nullable();
            $table->string('buyer_email');
            $table->string('product_version', 32)->default('latest');
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->unsignedInteger('max_downloads')->default(5);
            $table->unsignedInteger('downloads_used')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['buyer_email']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
