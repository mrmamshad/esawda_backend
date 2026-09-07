<?php

namespace App\Http\Resources\V1;

use Illuminate\Support\Str;

/** Compact public shop profile used by the Shops directory cards. */
class ShopResource extends BaseResource
{
    public function toArray($request): array
    {
        $imageBase = rtrim(config('app.url'), '/').'/storage/profile/';

        return [
            'id' => (int) $this->id,
            'username' => $this->username,
            'name' => $this->name ?: $this->username,
            'shop_name' => $this->shop_name,
            'shop_category' => $this->shop_category ?: null,
            'shop_category_slug' => $this->shop_category ? Str::slug($this->shop_category) : null,
            'shop_description' => $this->shop_description ?: null,
            'shop_verified' => !empty($this->shop_verified_at),
            'avatar_url' => $imageBase.($this->image ?: 'default_user.png'),
            'cover_url' => $this->cover ? $imageBase.$this->cover : null,
            'shop_banner_url' => $this->shop_banner ? $imageBase.$this->shop_banner : null,
            'online' => $this->bool($this->online),
            'location' => [
                'address' => $this->shop_address ?: $this->address,
                'city' => $this->city,
                'country' => $this->country,
            ],
            'stats' => [
                'active_products' => (int) ($this->active_products_count ?? 0),
                'total_products' => (int) ($this->posts_count ?? 0),
            ],
            'member_since' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
