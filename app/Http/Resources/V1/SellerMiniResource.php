<?php

namespace App\Http\Resources\V1;

/**
 * Compact seller card used inside the ad detail page's right sidebar.
 * Full seller profile shape lives in SellerResource.
 */
class SellerMiniResource extends BaseResource
{
    public function toArray($request): array
    {
        $base = rtrim(config('app.url'), '/').'/storage/profile/';

        return [
            'id' => (int) $this->id,
            'username' => $this->username,
            'name' => $this->name ?: $this->username,
            'is_shop' => (bool) $this->isShop(),
            'shop_name' => $this->shop_name ?: null,
            'shop_banner_url' => $this->shop_banner ? $base.$this->shop_banner : null,
            'avatar_url' => $base.($this->image ?: 'default_user.png'),
            'cover_url' => $this->cover ? $base.$this->cover : null,
            'online' => $this->bool($this->online),
            'member_since' => optional($this->created_at)->toIso8601String(),
            'phone' => $this->bool($this->hide_phone) ? null : $this->phone,
            'whatsapp' => $this->bool($this->hide_phone) ? null : ($this->whatsapp ?? $this->phone),
            'socials' => [
                'facebook' => $this->facebook,
                'twitter' => $this->twitter,
                'instagram' => $this->instagram,
                'linkedin' => $this->linkedin,
                'pinterest' => $this->pinterest ?? null,
            ],
        ];
    }
}
