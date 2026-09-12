<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Premium listing upgrade prices (Featured / Urgent / Highlight) plus the
 * Free / Premium listing-type switches shown on the Post a Product form.
 *
 * Checkout reads `upgrade_{featured,urgent,highlight}_price` from the
 * legacy `option` table and falls back to built-in defaults when a key is
 * missing, so this endpoint always reports the *effective* prices — what
 * the admin sees here is exactly what buyers pay.
 *
 * Listing types follow the same convention as the posting-rule toggles in
 * Settings (`shop_subscription_required`, `single_free_listings`): a key is
 * ON unless its stored value is exactly `'0'`. Missing keys default to ON
 * so old installs keep both options visible.
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

    public const LISTING_KEYS = [
        'free' => 'listing_free_enabled',
        'premium' => 'listing_premium_enabled',
    ];

    public function index()
    {
        $stored = Option::whereIn('option_name', array_merge(
            array_values(self::OPTION_KEYS),
            array_values(self::LISTING_KEYS),
            ['currency_code'],
        ))->pluck('option_value', 'option_name');

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
            'listing' => [
                'free_enabled' => ($stored[self::LISTING_KEYS['free']] ?? '1') !== '0',
                'premium_enabled' => ($stored[self::LISTING_KEYS['premium']] ?? '1') !== '0',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'featured' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'urgent' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'highlight' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'free_enabled' => ['nullable', 'boolean'],
            'premium_enabled' => ['nullable', 'boolean'],
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

        foreach (self::LISTING_KEYS as $flag => $key) {
            // `free_enabled` / `premium_enabled` arrive as booleans; absent
            // means "leave the current switch alone".
            $inputKey = $flag.'_enabled';
            if (!array_key_exists($inputKey, $data) || $data[$inputKey] === null || $data[$inputKey] === '') {
                continue;
            }

            Option::updateOrCreate(
                ['option_name' => $key],
                ['option_value' => $data[$inputKey] ? '1' : '0'],
            );
        }

        Cache::forget('meta.settings');
        Cache::forget('home.payload');

        return $this->index();
    }
}
