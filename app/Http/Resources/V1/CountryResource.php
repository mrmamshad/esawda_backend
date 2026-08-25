<?php

namespace App\Http\Resources\V1;

class CountryResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'code' => $this->code,
            'iso3' => $this->iso3,
            'name' => $this->name,
            'capital' => $this->capital,
            'currency_code' => $this->currency_code,
            'phone' => $this->phone,
            'continent' => $this->continent_code,
            'tld' => $this->tld,
            'active' => $this->bool($this->active),
        ];
    }
}
