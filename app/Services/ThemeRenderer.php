<?php

namespace App\Services;

use Illuminate\Contracts\View\View;

/**
 * Small helper that resolves theme-scoped Blade views.
 *
 * Legacy code loaded `templates/{tpl_name}/{name}.tpl`. Blade equivalent
 * lives at `resources/views/themes/{theme}/{name}.blade.php`.
 *
 * Usage:
 *   return app(ThemeRenderer::class)->render('index', ['popular_cities' => $cities]);
 */
class ThemeRenderer
{
    public function render(string $name, array $data = []): View
    {
        $theme = config('quickad.active_theme', 'thenext-theme');
        $data['link'] ??= $this->linkMap();
        // Auto-legacy-view fallback: try theme view first, else the converted
        // view under `themes.default.{name}` (which is a hand-written safe
        // stub written for controllers whose theme .tpl throws).
        $viewName = "themes.$theme.$name";
        if (!view()->exists($viewName)) {
            $viewName = "themes.default.$name";
        }

        return view($viewName, $data);
    }

    /** Build the legacy `$link[...]` global from named routes. */
    public function linkMap(): array
    {
        $map = [];
        foreach ((array) config('quickad.named_routes') as $legacy => $name) {
            try {
                $map[$legacy] = route($name);
            } catch (\Throwable) {
                $map[$legacy] = '#';
            }
        }

        return $map;
    }
}
