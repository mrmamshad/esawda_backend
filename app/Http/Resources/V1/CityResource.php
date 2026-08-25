<?php

namespace App\Http\Resources\V1;

class CityResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'country_code' => $this->country_code,
            'name' => $this->name,
            'ascii_name' => $this->asciiname,
            'lat' => $this->latitude !== null ? (float) $this->latitude : null,
            'lng' => $this->longitude !== null ? (float) $this->longitude : null,
            'population' => $this->population !== null ? (int) $this->population : null,
            'time_zone' => $this->time_zone,
            'active' => $this->bool($this->active),
        ];
    }
}
