<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Premium listing upgrade prices (Featured / Urgent / Highlight).
 *
 * Checkout reads `upgrade_{featured,urgent,highlight}_price` from the
 * legacy `option` table and falls back to built-in defaults when a key is
 * missing, so this endpoint always reports the *effective* prices — what
 * the admin sees here is exactly what buyers pay.
 */
class PremiumUpgradeAdminController extends Controller
{
    public const DEFAULTS = [
        'featured' => 200,
        'urgent' => 150,
        'highlight' => 100,
    ];

    public const OPTION_KEYS = [
        'featured' => 'upgrade_featured_price',
        'urgent' => 'upgrade_urgent_price',
        'highlight' => 'upgrade_highlight_price',
    ];

    public function index()
    {
        $stored = Option::whereIn('option_name', array_values(self::OPTION_KEYS))
            ->pluck('option_value', 'option_name');

        $prices = [];
        foreach (self::OPTION_KEYS as $flag => $key) {
            $raw = $stored[$key] ?? null;
            $prices[$flag] = $raw === null || $raw === ''
                ? self::DEFAULTS[$flag]
                : (float) $raw;
        }

        return $this->ok([
            'prices' => $prices,
            'defaults' => self::DEFAULTS,
            'currency' => (string) ($stored['currency_code'] ?? Option::where('option_name', 'currency_code')->value('option_value') ?? 'BDT'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'featured' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'urgent' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'highlight' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
        ]);

        foreach (self::OPTION_KEYS as $flag => $key) {
            // Blank means "leave the current value alone" — never wipe a
            // price by accident. Explicit 0 is a valid free upgrade.
            if (!array_key_exists($flag, $data) || $data[$flag] === null || $data[$flag] === '') {
                continue;
            }

            Option::updateOrCreate(
                ['option_name' => $key],
                ['option_value' => (string) $data[$flag]],
            );
        }

        Cache::forget('meta.settings');
        Cache::forget('home.payload');

        return $this->index();
    }
}
