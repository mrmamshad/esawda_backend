<?php

namespace App\Http\Resources\V1;

/**
 * Category (legacy: catagory_main).
 *
 * When `whenLoaded('subCategories')` is set the sub-tree is returned;
 * otherwise `ads_count` (if withCount) is exposed for sidebar badges.
 */
class CategoryResource extends BaseResource
{
    public function toArray($request): array
    {
        $iconBase = rtrim(config('app.url'), '/').'/storage/site/';
        // `picture` may be either a stored filename (relative to /storage/site/)
        // or a full external URL — guard so we don't emit /storage/site/https://…
        $pic = $this->picture;
        $picUrl = $pic
            ? (preg_match('~^https?://~i', $pic) ? $pic : $iconBase.ltrim($pic, '/'))
            : null;

        return [
            'id' => (int) $this->cat_id,
            'name' => $this->cat_name,
            'slug' => $this->slug,
            'order' => $this->cat_order !== null ? (int) $this->cat_order : null,
            'icon' => $this->icon,                                    // font-awesome class (legacy)
            'picture_url' => $picUrl,
            'ads_count' => $this->when(isset($this->ads_count), fn () => (int) $this->ads_count),
            'new_count' => $this->when(isset($this->new_count), fn () => (int) $this->new_count),
            'used_count' => $this->when(isset($this->used_count), fn () => (int) $this->used_count),
            'sub_categories' => SubCategoryResource::collection(
                $this->whenLoaded('subCategories')
            ),
        ];
    }
}
