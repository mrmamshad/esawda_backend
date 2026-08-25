<?php

namespace App\Http\Resources\V1;

class TestimonialResource extends BaseResource
{
    public function toArray($request): array
    {
        $base = rtrim(config('app.url'), '/').'/storage/testimonials/';

        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'designation' => $this->designation,
            'content' => $this->content,
            'avatar_url' => $this->image ? $base.$this->image : null,
        ];
    }
}
