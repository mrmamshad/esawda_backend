<?php

namespace App\Http\Resources\V1;

class FaqResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id'         => (int) $this->faq_id,
            'title'      => $this->faq_title,
            'content'    => $this->faq_content,
            'weight'     => (int) $this->faq_weight,
            'parent_id'  => $this->faq_pid ? (int) $this->faq_pid : null,
            'lang'       => $this->translation_lang,
            'active'     => $this->bool($this->active),
        ];
    }
}
