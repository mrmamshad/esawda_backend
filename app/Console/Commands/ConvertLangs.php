<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * php artisan quickad:convert-langs
 *
 * Ports all `includes/lang/lang_{code}.php` files (which set `$lang[...]`)
 * into Laravel translation files at `resources/lang/{code}/quickad.php`.
 *
 * Keys are lowercased so they align with the Blade output produced by
 * `TemplateBridge` (which emits `__('quickad.{lowercase_key}')`).
 */
class ConvertLangs extends Command
{
    protected $signature = 'quickad:convert-langs {--src=} {--dest=}';

    protected $description = 'Convert legacy Quickad lang_*.php files into Laravel translations.';

    /** Legacy filename → Laravel locale code. */
    protected array $localeMap = [
        'english' => 'en', 'french' => 'fr', 'german' => 'de',
        'spanish' => 'es', 'italian' => 'it', 'polish' => 'pl',
        'russian' => 'ru', 'arabic' => 'ar', 'chinese' => 'zh',
        'hindi' => 'hi', 'japanese' => 'ja', 'portuguese' => 'pt',
        'turkish' => 'tr', 'thai' => 'th', 'vietnamese' => 'vi',
        'romanian' => 'ro', 'bulgarian' => 'bg', 'hebrew' => 'he',
        'urdu' => 'ur', 'bangali' => 'bn', 'swedish' => 'sv',
    ];

    public function handle(): int
    {
        $src = $this->option('src') ?: base_path('../includes/lang');
        $dest = $this->option('dest') ?: resource_path('lang');

        if (!is_dir($src)) {
            $this->error("Source not found: $src");

            return self::FAILURE;
        }

        $count = 0;
        foreach (glob($src.'/lang_*.php') as $file) {
            $name = basename($file, '.php');
            $legacyName = preg_replace('/^lang_/', '', $name);
            $locale = $this->localeMap[$legacyName] ?? $legacyName;

            $lang = [];
            $contents = file_get_contents($file);
            // Match: $lang['KEY'] = 'value'; or "value";
            preg_match_all(
                "/\\\$lang\\['([^']+)'\\]\\s*=\\s*(['\"])(.*?)\\2\\s*;/s",
                $contents,
                $matches,
                PREG_SET_ORDER
            );
            foreach ($matches as $m) {
                $key = strtolower($m[1]);
                $val = $m[3];
                $lang[$key] = $val;
            }

            $outDir = $dest.'/'.$locale;
            if (!is_dir($outDir)) {
                mkdir($outDir, 0755, true);
            }
            $outFile = $outDir.'/quickad.php';
            $body = "<?php\n\nreturn ".var_export($lang, true).";\n";
            file_put_contents($outFile, $body);
            $this->line("  ✓ $locale (".count($lang)." keys) → $outFile");
            $count++;
        }
        $this->info("Converted $count language files.");

        return self::SUCCESS;
    }
}
