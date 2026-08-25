<?php

namespace App\Http\Resources\V1;

/**
 * SubCategory (legacy: catagory_sub).
 */
class SubCategoryResource extends BaseResource
{
    public function toArray($request): array
    {
        $iconBase = rtrim(config('app.url'), '/').'/storage/site/';
        $pic = $this->picture;
        $picUrl = $pic
            ? (preg_match('~^https?://~i', $pic) ? $pic : $iconBase.ltrim($pic, '/'))
            : null;

        return [
            'id' => (int) $this->sub_cat_id,
            'category_id' => (int) $this->main_cat_id,
            'name' => $this->sub_cat_name,
            'slug' => $this->slug,
            'order' => $this->cat_order !== null ? (int) $this->cat_order : null,
            'photo_show' => $this->bool($this->photo_show),
            'price_show' => $this->bool($this->price_show),
            'picture_url' => $picUrl,
            'ads_count' => $this->when(isset($this->ads_count), fn () => (int) $this->ads_count),
        ];
    }
}
