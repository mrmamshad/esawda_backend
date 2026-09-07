<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('transaction_id');
            $table->string('gateway', 40);
            $table->string('event_type', 40);
            $table->string('dedupe_key', 64);
            $table->string('status_code', 20)->nullable();
            $table->text('payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['gateway', 'dedupe_key'], 'payment_events_gateway_dedupe_unique');
            $table->index(['transaction_id', 'created_at']);
            $table->foreign('transaction_id')->references('id')->on('transaction')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
    }
};
