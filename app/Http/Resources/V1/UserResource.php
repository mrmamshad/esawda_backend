<?php

namespace App\Http\Resources\V1;

/**
 * Public-safe view of the current user (self). Includes contact info
 * and social handles that the profile editor needs.
 */
class UserResource extends BaseResource
{
    public function toArray($request): array
    {
        $imageBase = rtrim(config('app.url'), '/').'/storage/profile/';

        return [
            'id' => (int) $this->id,
            'username' => $this->username,
            'name' => $this->name ?: $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'user_type' => $this->user_type,
            'is_admin' => $this->isAdmin(),
            'is_shop' => $this->isShop(),
            'group_id' => $this->group_id,
            'plan_id' => $this->plan_id ? (int) $this->plan_id : null,
            'plan_active' => !empty($this->plan_expires_at) && $this->plan_expires_at->isFuture(),
            'plan_expires_at' => optional($this->plan_expires_at)->toIso8601String(),
            'ads_remaining' => (int) $this->ads_remaining,
            'shop_name' => $this->shop_name,
            'shop_category' => $this->shop_category,
            'shop_description' => $this->shop_description,
            'shop_address' => $this->shop_address,
            'shop_verified' => !empty($this->shop_verified_at),
            'shop_verified_at' => optional($this->shop_verified_at)->toIso8601String(),
            'shop_banner_url' => $this->shop_banner ? $imageBase.$this->shop_banner : null,
            'avatar_url' => $imageBase.($this->image && $this->image !== 'default_user.png' ? $this->image : 'default_user.png'),
            'avatar_set' => !empty($this->image) && $this->image !== 'default_user.png',
            'cover_url' => $this->cover ? $imageBase.$this->cover : null,
            'city' => $this->city,
            'country' => $this->country,
            'address' => $this->address,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'website' => $this->website,
            'socials' => [
                'facebook' => $this->facebook,
                'twitter' => $this->twitter,
                'instagram' => $this->instagram,
                'linkedin' => $this->linkedin,
                'youtube' => $this->youtube,
            ],
            'online' => $this->bool($this->online),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'last_active' => optional($this->lastactive)->toIso8601String(),
        ];
    }
}
