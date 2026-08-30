<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdPlacement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Public ad-placement read endpoint.
 *
 *   GET /api/v1/ads/placements?placements=home.after_categories,store.sidebar
 *
 * Returns the currently-live ad (image + optional link) for each requested
 * slot so the frontend AdSlot component can render a real admin-uploaded ad
 * instead of the static placeholder. Results are cached briefly; admin edits
 * bust the cache.
 */
class AdPlacementController extends Controller
{
    public function index(Request $request)
    {
        $slugs = collect(explode(',', (string) $request->query('placements', '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values();

        if ($slugs->isEmpty()) {
            return $this->ok([]);
        }

        $key = 'ads.placements.'.md5(implode(',', $slugs->all()));

        $rows = Cache::remember($key, 120, function () use ($slugs) {
            return AdPlacement::live()
                ->whereIn('slug', $slugs)
                ->get();
        });

        $out = [];
        foreach ($rows as $ad) {
            $out[$ad->slug] = [
                'slug' => $ad->slug,
                'title' => $ad->title,
                'image_url' => $ad->image_url,
                'link_url' => $ad->link_url,
                'alt_text' => $ad->alt_text,
            ];
        }

        return $this->ok($out);
    }
}
