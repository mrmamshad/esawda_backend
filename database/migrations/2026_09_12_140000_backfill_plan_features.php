<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill `settings.features` for plans created before offer bullets
 * became admin-editable, using the exact automatic bullets the shop page
 * renders for them. No visual change — the admin Edit modal simply shows
 * (and can then edit) the real list instead of an empty box.
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
            $plans = DB::table('plans')->select('id', 'settings')->get();
        } catch (Throwable) {
            return;
        }

        foreach ($plans as $plan) {
            $settings = is_string($plan->settings)
                ? (json_decode($plan->settings, true) ?: [])
                : (is_array($plan->settings) ? $plan->settings : []);
            if (!is_array($settings)) {
                $settings = [];
            }

            $existing = $settings['features'] ?? null;
            if (is_array($existing) && count(array_filter(array_map(
                fn ($line) => trim((string) $line),
                $existing
            ))) > 0) {
                continue;
            }

            $adsLimit = (int) ($settings['ads_limit'] ?? 0);
            $featuredAds = (int) ($settings['featured_ads'] ?? 0);
            $durationDays = (int) ($settings['duration_days'] ?? 0);

            $settings['features'] = [
                $adsLimit > 0 ? "{$adsLimit} product listings" : 'Flexible product listings',
                $featuredAds > 0 ? "{$featuredAds} featured product boosts" : 'Standard marketplace visibility',
                $durationDays > 0 ? "{$durationDays}-day listing duration" : 'Long-running product visibility',
                'Buyer messaging and sales dashboard',
                'Shop performance insights',
            ];

            DB::table('plans')->where('id', $plan->id)->update([
                'settings' => json_encode($settings),
            ]);
        }
    }

    public function down(): void
    {
        // One-way backfill — clearing would erase admin edits made since.
    }
};
