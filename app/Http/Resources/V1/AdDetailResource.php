<?php

namespace App\Http\Resources\V1;

/**
 * Full ad payload for the /ads/{id-slug} detail page. Includes gallery,
 * seller card data, custom-field values, and lifecycle timestamps.
 */
class AdDetailResource extends BaseResource
{
    public function toArray($request): array
    {
        $images = $this->images($this->screen_shot);

        return [
            'id'          => (int) $this->id,
            'slug'        => $this->slug,
            'url_slug'    => $this->id . '-' . ($this->slug ?: 'ad'),
            'title'       => $this->product_name,
            'description' => $this->description,
            'price'       => (int) $this->price,
            'negotiable'  => $this->bool($this->negotiable),
            'phone'       => $this->bool($this->hide_phone) ? null : $this->phone,
            'tags'        => $this->tag ? preg_split('/\s*,\s*/', $this->tag, -1, PREG_SPLIT_NO_EMPTY) : [],
            'view_count'  => (int) $this->view,
            'featured'    => $this->bool($this->featured),
            'urgent'      => $this->bool($this->urgent),
            'highlight'   => $this->bool($this->highlight),
            'images'      => $images,
            'location'    => [
                'city'    => $this->city,
                'state'   => $this->state,
                'country' => $this->country,
                'address' => $this->location,
                'coords'  => $this->latLng($this->latlong),
            ],
            'category'     => $this->when($this->relationLoaded('category'), fn () => (
                ($c = $this->getRelation('category')) ? [
                    'id'   => (int) $c->cat_id,
                    'name' => $c->cat_name,
                    'slug' => $c->slug,
                    'icon' => $c->icon,
                ] : null
            )),
            'sub_category' => $this->when($this->relationLoaded('subCategory'), fn () => (
                ($sc = $this->getRelation('subCategory')) ? [
                    'id'   => (int) $sc->sub_cat_id,
                    'name' => $sc->sub_cat_name,
                    'slug' => $sc->slug,
                ] : null
            )),
            'seller'       => $this->when($this->relationLoaded('user'), fn () => (
                ($u = $this->getRelation('user')) ? (new SellerMiniResource($u))->resolve() : null
            )),
            'custom_fields'=> $this->whenLoaded('customData', fn () =>
                $this->getRelation('customData')->map(fn ($cd) => [
                    'field_id' => (int) $cd->field_id,
                    'type'     => $cd->field_type,
                    'value'    => $cd->field_data,
                ])->values()
            ),
            'expires_at'   => $this->unixToIso($this->expire_date),
            'created_at'   => optional($this->created_at)->toIso8601String(),
            'updated_at'   => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
