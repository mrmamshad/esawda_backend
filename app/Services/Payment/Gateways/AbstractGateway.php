<?php

namespace App\Services\Payment\Gateways;

use App\Models\Transaction;
use App\Services\Payment\PaymentGatewayInterface;

/**
 * Base class every concrete gateway extends. Provides shared helpers
 * (transaction persistence, IP recording, config lookup) so subclasses
 * only implement the gateway-specific initiate/verify calls.
 */
abstract class AbstractGateway implements PaymentGatewayInterface
{
    abstract public function slug(): string;
    abstract public function label(): string;

    public function initiate(Transaction $tx): mixed
    {
        // Default: no-op — subclasses override with real redirect/API call.
        $tx->status = 'pending';
        $tx->transaction_gatway = $this->slug();
        $tx->save();
        return route('payment', ['access_token' => $tx->id, 'i' => $this->slug()]);
    }

    public function handleCallback(array $payload): Transaction
    {
        $tx = Transaction::findOrFail($payload['transaction_id'] ?? 0);
        $tx->status  = $payload['status'] ?? 'success';
        $tx->payment_id = $payload['payment_id'] ?? null;
        $tx->save();
        return $tx;
    }

    public function verify(Transaction $tx): bool
    {
        return $tx->status === 'success';
    }

    /** Shortcut for reading `config('quickad.gateways.{slug}.key')` etc. */
    protected function conf(string $key, mixed $default = null): mixed
    {
        return config("quickad.gateways.{$this->slug()}.$key", $default);
    }
}
