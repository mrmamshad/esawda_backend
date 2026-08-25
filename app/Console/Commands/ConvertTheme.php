<?php

namespace App\Console\Commands;

use App\Services\TemplateBridge;
use Illuminate\Console\Command;

/**
 * php artisan quickad:convert-theme thenext-theme
 *
 * Converts legacy `.tpl` files under
 *   ../templates/{theme}/*.tpl
 * into Blade files under
 *   resources/views/themes/{theme}/*.blade.php
 */
class ConvertTheme extends Command
{
    protected $signature = 'quickad:convert-theme {theme=thenext-theme} {--src=} {--dest=}';

    protected $description = 'Convert a legacy Quickad .tpl theme into Blade views.';

    public function handle(TemplateBridge $bridge): int
    {
        $theme = $this->argument('theme');
        $src = $this->option('src') ?: base_path('../templates/'.$theme);
        $dest = $this->option('dest') ?: resource_path('views/themes/'.$theme);

        $this->info("Converting theme: $theme");
        $this->line("  src : $src");
        $this->line("  dest: $dest");

        $files = $bridge->convertDirectory($src, $dest);
        $this->info(count($files).' file(s) converted.');
        foreach ($files as $f) {
            $this->line('  ✓ '.str_replace(base_path().'/', '', $f));
        }

        return self::SUCCESS;
    }
}
