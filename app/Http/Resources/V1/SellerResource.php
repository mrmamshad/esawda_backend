<?php

namespace App\Http\Resources\V1;

/**
 * Full seller profile — powers the /seller/[username] hero banner:
 *   avatar, name, "Active Member" + Online dot, Sold / Total Listing
 *   stat blocks, socials row, Message + Whatsapp buttons, location.
 *
 * The controller enriches the User model with `ads_total`, `ads_sold`,
 * and `avg_rating` via withCount/subquery before wrapping.
 */
class SellerResource extends BaseResource
{
    public function toArray($request): array
    {
        $avatarBase = rtrim(config('app.url'), '/') . '/storage/profile/';
        return [
            'id'          => (int) $this->id,
            'username'    => $this->username,
            'name'        => $this->name ?: $this->username,
            'tagline'     => $this->tagline,
            'description' => $this->description,
            'avatar_url'  => $avatarBase . ($this->image ?: 'default_user.png'),
            'online'      => $this->bool($this->online),
            'phone'       => $this->phone,
            'whatsapp'    => $this->whatsapp ?? $this->phone,
            'website'     => $this->website,
            'location'    => [
                'city'    => $this->city,
                'country' => $this->country,
                'address' => $this->address,
            ],
            'socials'     => [
                'facebook'  => $this->facebook,
                'twitter'   => $this->twitter,
                'instagram' => $this->instagram,
                'linkedin'  => $this->linkedin,
                'youtube'   => $this->youtube,
                'pinterest' => $this->pinterest ?? null,
            ],
            'stats'       => [
                'total_listings' => (int) ($this->ads_total ?? 0),
                'sold'           => (int) ($this->ads_sold  ?? 0),
                'active'         => (int) ($this->ads_active ?? 0),
                'avg_rating'     => $this->avg_rating !== null ? round((float) $this->avg_rating, 2) : null,
                'reviews_count'  => (int) ($this->reviews_count ?? 0),
            ],
            'member_since'=> optional($this->created_at)->toIso8601String(),
            'last_active' => optional($this->lastactive)->toIso8601String(),
        ];
    }
}
