<?php

namespace App\Http\Resources\V1;

class LanguageResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'direction' => $this->direction ?: 'ltr',
            'active' => $this->bool($this->active),
            'default' => $this->bool($this->default),
        ];
    }
}
