<?php

namespace App\Http\Resources\V1;

class PlanResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'badge' => $this->badge,
            'monthly_price' => (float) $this->monthly_price,
            'annual_price' => (float) $this->annual_price,
            'lifetime_price' => (float) $this->lifetime_price,
            'recommended' => $this->recommended === 'yes',
            'settings' => $this->settings ? (json_decode($this->settings, true) ?: $this->settings) : null,
            'active' => (int) $this->status === 1,
            'created_at' => optional($this->date)
                                    ? (is_string($this->date) ? $this->date : $this->date->toIso8601String())
                                    : null,
        ];
    }
}
