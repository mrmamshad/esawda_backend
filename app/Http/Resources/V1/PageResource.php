<?php

namespace App\Http\Resources\V1;

class PageResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'title' => $this->title,
            'content' => $this->content,
            'lang' => $this->translation_lang,
            'parent_id' => $this->parent_id ? (int) $this->parent_id : null,
            'active' => $this->bool($this->active),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
