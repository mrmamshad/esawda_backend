<?php

namespace App\Http\Resources\V1;

/**
 * Review shown in the testimonials strip on the Seller Profile page.
 * The controller eager-loads the author user; a slim shape is emitted
 * so the frontend can render <TestimonialCard/> without a second call.
 */
class ReviewResource extends BaseResource
{
    public function toArray($request): array
    {
        $avatarBase = rtrim(config('app.url'), '/').'/storage/profile/';
        $author = $this->relationLoaded('user') ? $this->getRelation('user') : null;

        return [
            'id' => (int) $this->reviewID,
            'rating' => $this->rating !== null ? round((float) $this->rating, 1) : null,
            'comment' => $this->comments,
            'image' => $this->image ? url('storage/'.$this->image) : null,
            'date' => $this->date,
            'author' => $author ? [
                'id' => (int) $author->id,
                'username' => $author->username,
                'name' => $author->name ?: $author->username,
                'avatar_url' => $avatarBase.($author->image ?: 'default_user.png'),
                'tagline' => $author->tagline,
            ] : null,
            'product_id' => (int) $this->productID,
        ];
    }
}
