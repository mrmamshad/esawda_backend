<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expiry timestamp for the password-reset token (`forgot` column).
     * Nullable so existing rows (no pending reset) stay valid.
     */
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dateTime('forgot_expires_at')->nullable()->after('forgot');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('forgot_expires_at');
        });
    }
};
