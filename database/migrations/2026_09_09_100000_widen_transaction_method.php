<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gateway wallet/bank names (e.g. "Dutch-Bangla Bank Ltd") were cut
     * off at 20 chars. Widen so the full method name is stored.
     */
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->string('transaction_method', 60)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->string('transaction_method', 20)->nullable()->change();
        });
    }
};
