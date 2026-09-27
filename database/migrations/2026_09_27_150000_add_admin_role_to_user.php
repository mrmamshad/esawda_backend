<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Unprefixed — the connection adds the `ad_` prefix → ad_user.
    private string $table = 'user';

    public function up(): void
    {
        if (!Schema::hasColumn($this->table, 'admin_role')) {
            Schema::table($this->table, function (Blueprint $table) {
                // 'full'  = unrestricted admin (default, back-compat)
                // 'limited' = restricted admin (cannot see Users / Transactions)
                $table->string('admin_role', 16)->default('full')->after('user_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->table, 'admin_role')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('admin_role');
            });
        }
    }
};
