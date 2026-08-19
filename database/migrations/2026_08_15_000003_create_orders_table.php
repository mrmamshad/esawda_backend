<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('buyer_id');
            $table->unsignedInteger('seller_id');
            $table->unsignedInteger('transaction_id')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('shipping_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->string('courier_name', 100)->nullable();
            $table->string('tracking_no', 100)->nullable();
            $table->boolean('seller_paid')->default(false);
            $table->timestamps();

            $table->index(['seller_id', 'shipping_status']);
            $table->index('buyer_id');
            $table->index('transaction_id');
            $table->foreign('product_id')->references('id')->on('product')->cascadeOnDelete();
            $table->foreign('buyer_id')->references('id')->on('user');
            $table->foreign('seller_id')->references('id')->on('user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};