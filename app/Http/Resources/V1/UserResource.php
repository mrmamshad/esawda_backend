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
        $imageBase = rtrim(config('app.url'), '/') . '/storage/profile/';
        return [
            'id'         => (int) $this->id,
            'username'   => $this->username,
            'name'       => $this->name ?: $this->username,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'user_type'  => $this->user_type,
            'is_admin'   => $this->isAdmin(),
            'is_shop'    => $this->isShop(),
            'group_id'   => $this->group_id,
            'avatar_url' => $imageBase . ($this->image ?: 'default_user.png'),
            'city'       => $this->city,
            'country'    => $this->country,
            'address'    => $this->address,
            'tagline'    => $this->tagline,
            'description'=> $this->description,
            'website'    => $this->website,
            'socials'    => [
                'facebook'  => $this->facebook,
                'twitter'   => $this->twitter,
                'instagram' => $this->instagram,
                'linkedin'  => $this->linkedin,
                'youtube'   => $this->youtube,
            ],
            'online'      => $this->bool($this->online),
            'created_at'  => optional($this->created_at)->toIso8601String(),
            'last_active' => optional($this->lastactive)->toIso8601String(),
        ];
    }
}
