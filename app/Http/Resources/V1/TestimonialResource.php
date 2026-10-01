<?php

namespace App\Http\Resources\V1;

class TestimonialResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'designation' => $this->designation,
            'content' => $this->content,
            // Reuses the model accessor so legacy bare filenames
            // (`rubaiya.jpg`) and Filament uploads
            // (`testimonials/rubaiya.jpg`) both resolve correctly.
            'avatar_url' => $this->image_url,
        ];
    }
}
