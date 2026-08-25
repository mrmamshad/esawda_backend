<?php

namespace App\Services;

/**
 * Converts the legacy Bylancer `HtmlTemplate` (.tpl) syntax into Blade.
 *
 * Legacy grammar (see `includes/classes/class.template_engine.php`):
 *   {VAR}                              placeholder for SetParameter('VAR', $v)
 *   {LANG_XXX}                         translation string
 *   {LINK_XXX}                         URL from the `$link` global
 *   IF(cond){ ... {:IF}                inline conditional (single-line cond)
 *   <!-- BEGIN LOOP name --> ... <!-- END LOOP -->
 *
 * Output Blade:
 *   {VAR}      → {{ $var }}
 *   {LANG_X}   → {{ __('quickad.X') }}
 *   {LINK_X}   → {{ $link['X'] ?? '#' }}
 *   IF(a==b){  → @if(a==b)
 *   {:IF}      → @endif
 *   BEGIN LOOP → @foreach($loop as $item)
 *   END LOOP   → @endforeach
 */
class TemplateBridge
{
    /** Convert a raw .tpl string to Blade markup. */
    public function convert(string $tpl): string
    {
        $out = $tpl;

        // --- 1. Custom IF(...) { ... {:IF}  →  @if(...) ... @endif
        // Rewrite {:IF} first so nested IF() blocks translate cleanly.
        $out = preg_replace('/\{:IF\}/', '@endif', $out);
        $out = preg_replace_callback(
            '/\bIF\s*\((.+?)\)\s*\{/s',
            fn ($m) => '@if('.$this->normalizeExpression($m[1]).')',
            $out
        );

        // --- 2. <!-- BEGIN LOOP name --> ... <!-- END LOOP -->
        $out = preg_replace_callback(
            '/<!--\s*BEGIN\s+LOOP\s+([a-zA-Z0-9_]+)\s*-->/',
            fn ($m) => '@foreach($'.strtolower($m[1]).' ?? [] as $item)',
            $out
        );
        $out = preg_replace('/<!--\s*END\s+LOOP\s*-->/', '@endforeach', $out);

        // --- 2b. Legacy inline loop syntax: {LOOP: NAME} ... {/LOOP: NAME}
        //     with body references like {NAME.field}
        $out = preg_replace_callback(
            '/\{LOOP:\s*([A-Z0-9_]+)\s*\}/',
            fn ($m) => '@foreach($'.strtolower($m[1]).' ?? [] as $'.strtolower($m[1]).')',
            $out
        );
        $out = preg_replace('/\{\/LOOP:\s*[A-Z0-9_]+\s*\}/', '@endforeach', $out);
        // {NAME.field} → {{ $name['field'] ?? $name->field ?? '' }}  (works for both arrays + Eloquent)
        $out = preg_replace_callback(
            '/\{([A-Z][A-Z0-9_]*)\.([a-zA-Z0-9_]+)\}/',
            fn ($m) => '{{ data_get($'.strtolower($m[1])." ?? [], '".$m[2]."', '') }}",
            $out
        );

        // --- 3. Translation strings {LANG_XXX} → {{ __('quickad.XXX') }}
        $out = preg_replace_callback(
            '/\{LANG_([A-Z0-9_]+)\}/',
            fn ($m) => "{{ __('quickad.".strtolower($m[1])."') }}",
            $out
        );

        // --- 4. Named links {LINK_XXX} → {{ $link['XXX'] ?? '#' }}
        $out = preg_replace_callback(
            '/\{LINK_([A-Z0-9_-]+)\}/',
            fn ($m) => "{{ \$link['".$m[1]."'] ?? '#' }}",
            $out
        );

        // --- 5. Loop item {item.field} → {{ $item['field'] }}
        $out = preg_replace_callback(
            '/\{item\.([a-zA-Z0-9_]+)\}/',
            fn ($m) => "{{ \$item['".$m[1]."'] ?? '' }}",
            $out
        );

        // --- 6. Overall header/footer sub-templates → @include
        $out = str_replace('{OVERALL_HEADER}', "@include('partials.header')", $out);
        $out = str_replace('{OVERALL_FOOTER}', "@include('partials.footer')", $out);

        // --- 7. Any remaining plain {VAR_NAME} → {{ $var_name ?? '' }}
        //     (Runs LAST so {LANG_..} / {LINK_..} are already resolved.)
        $out = preg_replace_callback(
            '/\{([A-Z_][A-Z0-9_]*)\}/',
            fn ($m) => '{{ $'.strtolower($m[1])." ?? '' }}",
            $out
        );

        // --- 8. POST-PASS: strip `"{{ ... }}"` echoes inside @if/@elseif.
        //     Blade forbids echoes inside directives — so unwrap into
        //     plain PHP. Works line-by-line; supports chained OR/AND.
        $out = implode("\n", array_map(function (string $line): string {
            if (!str_contains($line, '@if(') && !str_contains($line, '@elseif(')) {
                return $line;
            }

            // For each @if(...) or @elseif(...) on this line, unwrap the
            // inner "{{ ... }}" echoes.
            return preg_replace_callback(
                '/@(if|elseif)\(((?:[^()]|\([^)]*\))*)\)/',
                function ($m) {
                    $inner = $m[2];
                    if (!str_contains($inner, '{{')) {
                        return $m[0];
                    }
                    $inner = preg_replace('/"\{\{\s*(.+?)\s*\}\}"/', '($1)', $inner);

                    return '@'.$m[1].'('.$inner.')';
                },
                $line
            );
        }, explode("\n", $out)));

        return $out;
    }

    /** Convert legacy IF() argument syntax to valid PHP. */
    private function normalizeExpression(string $expr): string
    {
        // Legacy uses '{VAR}' inside IF() — rewrite to $var reference.
        $expr = preg_replace_callback(
            '/[\'"]?\{([A-Z_][A-Z0-9_]*)\}[\'"]?/',
            fn ($m) => '($'.strtolower($m[1]).' ?? "")',
            $expr
        );

        return trim($expr);
    }

    /** Bulk convert an entire theme directory. */
    public function convertDirectory(string $srcDir, string $destDir): array
    {
        if (!is_dir($srcDir)) {
            throw new \InvalidArgumentException("Source directory not found: $srcDir");
        }
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        $converted = [];
        foreach (glob($srcDir.'/*.tpl') as $tplFile) {
            $name = pathinfo($tplFile, PATHINFO_FILENAME);
            $blade = $this->convert(file_get_contents($tplFile));
            $out = $destDir.'/'.$name.'.blade.php';
            file_put_contents($out, $blade);
            $converted[] = $out;
        }

        return $converted;
    }
}
