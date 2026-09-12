<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * No plan is highlighted by default — cards stay normal until hovered,
 * unless the admin explicitly ticks "Most popular" on a plan.
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::table('plans')->where('recommended', 'yes')->update(['recommended' => 'no']);
        } catch (Throwable) {
            // Fresh installs have no rows yet — nothing to clear.
        }
    }

    public function down(): void
    {
        // One-way: previous flags are not retained.
    }
};
