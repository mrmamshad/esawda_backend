<?php

namespace App\Http\Resources\V1;

/**
 * Short-form ad — used in every grid, rail, and search hit.
 * `AdDetailResource` extends this with description, gallery, seller, etc.
 */
class AdResource extends BaseResource
{
    public function toArray($request): array
    {
        $images = $this->images($this->screen_shot);
        $thumb  = $images[0]['thumb'] ?? null;

        // When the seller hasn't uploaded any pictures we still want a
        // presentable card everywhere the ad appears. Generate an SVG
        // placeholder tinted from the ad title so every card gets a
        // unique-looking (but consistent per-ad) tile.
        if (! $thumb) {
            $thumb = self::placeholderSvg((string) $this->product_name);
        }

        return [
            'id'          => (int) $this->id,
            'slug'        => $this->slug,
            'url_slug'    => $this->id . '-' . ($this->slug ?: 'ad'),   // frontend URL segment
            'title'       => $this->product_name,
            'price'       => (int) $this->price,
            'negotiable'  => $this->bool($this->negotiable),
            'condition'   => $this->condition ?? 'used',   // 'new' | 'used'
            'status'      => (string) $this->status,       // draft|pending|active|sold_out|removed|expire|rejected
            'thumbnail'   => $thumb,
            'featured'    => $this->bool($this->featured),
            'urgent'      => $this->bool($this->urgent),
            'highlight'   => $this->bool($this->highlight),
            'location'    => [
                'city'    => $this->city,
                'state'   => $this->state,
                'country' => $this->country,
            ],
            // NB: legacy `category` column collides with the `category`
            // relation name. When the relation is loaded Eloquent stores
            // it under getRelation('category'); we read it explicitly to
            // avoid the collision.
            'category'    => $this->when($this->relationLoaded('category'), fn () => (
                ($c = $this->getRelation('category')) ? [
                    'id'   => (int) $c->cat_id,
                    'name' => $c->cat_name,
                    'slug' => $c->slug,
                ] : null
            )),
            'sub_category' => $this->when($this->relationLoaded('subCategory'), fn () => (
                ($sc = $this->getRelation('subCategory')) ? [
                    'id'   => (int) $sc->sub_cat_id,
                    'name' => $sc->sub_cat_name,
                    'slug' => $sc->slug,
                ] : null
            )),
            'created_at'   => optional($this->created_at)->toIso8601String(),
        ];
    }

    /**
     * Build a lightweight tinted SVG data-URI so cards always look filled.
     * The hue is derived from the title so the same ad always renders the
     * same colour, and different ads look distinct in a grid.
     */
    private static function placeholderSvg(string $title): string
    {
        // Emerald-family palette pulled from the Stitch design system.
        static $palette = [
            ['#004D40', '#006C4F'], ['#00342B', '#004D40'], ['#006C4F', '#1EB286'],
            ['#0F6B4F', '#149A6B'], ['#00251E', '#004D40'], ['#1EB286', '#59DDAE'],
        ];
        $key   = abs(crc32($title));
        $pair  = $palette[$key % count($palette)];
        $letter = mb_strtoupper(mb_substr(trim($title) ?: '?', 0, 1));

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$pair[0]}"/>
      <stop offset="100%" stop-color="{$pair[1]}"/>
    </linearGradient>
  </defs>
  <rect width="400" height="300" fill="url(#g)"/>
  <text x="50%" y="50%" text-anchor="middle" dominant-baseline="central"
        font-family="Inter, Arial, sans-serif" font-size="120" font-weight="700"
        fill="rgba(255,255,255,0.25)">{$letter}</text>
</svg>
SVG;

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}
