<?php

namespace App\Services;

use App\Models\Option;
use App\Models\User;

/**
 * Single source of truth for "may this account publish a listing?".
 *
 * Layers (first match wins):
 *   1. post_policy = 'blocked' → never (free or paid).
 *   2. post_policy = 'free'    → always, quota untouched.
 *   3. 'inherit' + shop        → global `shop_subscription_required`
 *      decides: on  = needs an unexpired plan with quota left,
 *                 off = free.
 *   4. 'inherit' + single user → global `single_free_listings`
 *      decides: on  = free (default — classifieds stay open),
 *                 off = needs plan + quota like a shop.
 */
class PostingPolicy
{
    public static function isBlocked(User $user): bool
    {
        return (string) ($user->post_policy ?? 'inherit') === 'blocked';
    }

    public static function canPostFree(User $user): bool
    {
        $policy = (string) ($user->post_policy ?? 'inherit');

        if ($policy === 'blocked') {
            return false;
        }
        if ($policy === 'free') {
            return true;
        }

        if ($user->isShop()) {
            if (self::option('shop_subscription_required', '1') !== '1') {
                return true;
            }
        } elseif (self::option('single_free_listings', '1') === '1') {
            return true;
        }

        $hasPlan = !empty($user->plan_expires_at) && $user->plan_expires_at->isFuture();

        return $hasPlan && (int) $user->ads_remaining > 0;
    }

    /**
     * True when a successful free post should burn one subscription slot —
     * i.e. the quota is what entitled this post. Policy-free and
     * globally-free posts never touch the quota.
     */
    public static function consumesQuota(User $user): bool
    {
        if ((string) ($user->post_policy ?? 'inherit') !== 'inherit') {
            return false;
        }
        if (!$user->plan_expires_at || !$user->plan_expires_at->isFuture()) {
            return false;
        }
        if ((int) $user->ads_remaining <= 0) {
            return false;
        }
        if ($user->isShop()) {
            return self::option('shop_subscription_required', '1') === '1';
        }

        return self::option('single_free_listings', '1') !== '1';
    }

    private static function option(string $key, string $default): string
    {
        return (string) (Option::where('option_name', $key)->value('option_value') ?? $default);
    }
}
