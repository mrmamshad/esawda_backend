<?php

namespace App\Http\Resources\V1;

class CurrencyResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => (int) $this->id,
            'code'               => $this->code,
            'name'               => $this->name,
            'symbol'             => $this->html_entity,
            'in_left'            => $this->bool($this->in_left),
            'decimal_places'     => (int) $this->decimal_places,
            'decimal_separator'  => $this->decimal_separator,
            'thousand_separator' => $this->thousand_separator,
        ];
    }
}
