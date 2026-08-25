<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CurrencyResource;
use App\Http\Resources\V1\LanguageResource;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Option;
use Illuminate\Support\Facades\Cache;

/**
 * Site-wide meta lookups. Everything here is public, cacheable at the
 * edge, and never depends on the authenticated user.
 */
class MetaController extends Controller
{
    public function currencies()
    {
        return $this->ok(
            CurrencyResource::collection(
                Cache::remember('meta.currencies', 300, fn () => Currency::orderBy('code')->get())
            )
        );
    }

    public function languages()
    {
        return $this->ok(LanguageResource::collection(
            Cache::remember('meta.languages', 300, fn () => Language::where('active', 1)->orderByDesc('default')->orderBy('name')->get())
        ));
    }

    /**
     * Small key/value settings the frontend needs at boot (site name,
     * default currency, contact email, feature flags). Sourced from the
     * legacy `options` table if present, otherwise sensible defaults.
     */
    public function settings()
    {
        $defaults = [
            'site_name' => config('app.name', 'offersale.'),
            'default_currency' => 'BDT',
            'currency_symbol' => '৳',
            'currency_code' => 'BDT',
            'default_locale' => config('app.locale', 'en'),
            'features' => [
                'chat' => true,
                'favourites' => true,
                'reviews' => true,
                'featured_ads' => true,
            ],
        ];

        return $this->ok(['settings' => Cache::remember('meta.settings', 300, function () use ($defaults) {
            try {
                $rows = Option::all(['option_name', 'option_value'])
                    ->pluck('option_value', 'option_name')->toArray();

                return array_replace($defaults, $rows);
            } catch (\Throwable) {
                return $defaults;
            }
        })]);
    }
}
