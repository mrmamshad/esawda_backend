<?php

namespace App\Http\Resources\V1;

class BlogResource extends BaseResource
{
    public function toArray($request): array
    {
        $imgBase = rtrim(config('app.url'), '/').'/storage/blog/';
        $avatarBase = rtrim(config('app.url'), '/').'/storage/profile/';
        $author = $this->relationLoaded('author') ? $this->getRelation('author') : null;

        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'slug' => \Str::slug($this->title),
            'url_slug' => $this->id.'-'.\Str::slug($this->title),
            'excerpt' => \Str::limit(strip_tags($this->description), 200),
            'description' => $this->description,
            'image_url' => $this->image
                                ? (preg_match('~^https?://~i', $this->image)
                                    ? $this->image
                                    : $imgBase.ltrim($this->image, '/'))
                                : null,
            'tags' => $this->tags ? preg_split('/\s*,\s*/', $this->tags, -1, PREG_SPLIT_NO_EMPTY) : [],
            'status' => $this->status,
            'author' => $author ? [
                'id' => (int) $author->id,
                'username' => $author->username,
                'name' => $author->name ?: $author->username,
                'avatar_url' => $avatarBase.($author->image ?: 'default_user.png'),
            ] : null,
            'categories' => $this->whenLoaded('categories', fn () => (
                $this->getRelation('categories')->map(fn ($c) => [
                    'id' => (int) $c->id,
                    'title' => $c->title,
                    'slug' => $c->slug,
                ])->values()
            )),
            'created_at' => optional($this->created_at)
                                ? (is_string($this->created_at) ? $this->created_at : $this->created_at->toIso8601String())
                                : null,
        ];
    }
}
