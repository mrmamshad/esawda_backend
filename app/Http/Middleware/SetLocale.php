<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Applies the user's preferred locale on every request. Sources checked
 * in priority order:
 *   1. `?lang=xx` query — one-shot override
 *   2. `lang` session key — persistent selection
 *   3. `app.fallback_locale` — default from config
 */
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $available = ['en','fr','de','es','it','pt','ru','ar','hi','ja','tr','zh','pl','bn','vi','th','ro','bg','he','ur','sv'];

        if ($request->has('lang')) {
            $lang = $request->query('lang');
            if (in_array($lang, $available, true)) {
                session(['lang' => $lang]);
            }
        }
        $current = session('lang', config('app.locale', 'en'));
        if (in_array($current, $available, true)) {
            App::setLocale($current);
        }
        return $next($request);
    }
}
