<?php

namespace App\Http\Resources\V1;

class TransactionResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->id,
            'plan_name' => $this->plan_name,
            'plan_id' => $this->plan_id ? (int) $this->plan_id : null,
            'amount' => (float) ($this->amount ?? 0),
            'currency' => $this->currency ?? 'USD',
            'method' => $this->method,
            'status' => $this->status?->value ?? 'pending',
            'reference' => $this->reference,
            'invoice_url' => $this->id ? url('/api/v1/me/transactions/'.$this->id.'/invoice') : null,
            'created_at' => optional($this->created_at)
                                ? (is_string($this->created_at) ? $this->created_at : $this->created_at->toIso8601String())
                                : null,
        ];
    }
}
