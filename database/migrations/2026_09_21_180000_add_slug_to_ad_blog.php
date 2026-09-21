<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    // Table name WITHOUT the connection's `ad_` prefix — Laravel adds it.
    private string $table = 'blog';

    public function up(): void
    {
        if (!Schema::hasColumn($this->table, 'slug')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->string('slug', 200)->nullable()->after('title');
            });
        }

        // Backfill slugs for existing posts from their titles.
        foreach (DB::table($this->table)->select('id', 'title', 'slug')->get() as $row) {
            if (empty($row->slug)) {
                DB::table($this->table)
                    ->where('id', $row->id)
                    ->update(['slug' => Str::slug((string) $row->title)]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->table, 'slug')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
