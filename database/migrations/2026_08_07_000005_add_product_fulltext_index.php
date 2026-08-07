<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * FULLTEXT index for ad search. `searchSuggest` and Filterable currently
 * do `LIKE '%needle%'` across product_name + description + tag — a leading-
 * wildcard scan an ordinary B-tree can't serve. A FULLTEXT index enables a
 * MATCH ... AGAINST rewrite (out of the LIKE path entirely). Not swapped in
 * here so behaviour is unchanged; the index is the prerequisite for that.
 *
 * Guarded: FULLTEXT requires MySQL/MariaDB InnoDB; skipped on sqlite/tests.
 */
return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('product')) return;
        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) return;

        DB::statement('ALTER TABLE ' . DB::getTablePrefix() . 'product ADD FULLTEXT idx_product_fulltext (product_name, description, tag)');
    }

    public function down(): void
    {
        if (! Schema::hasTable('product')) return;
        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) return;

        try {
            DB::statement('ALTER TABLE ' . DB::getTablePrefix() . 'product DROP INDEX idx_product_fulltext');
        } catch (\Throwable $e) { /* ignore */ }
    }
};