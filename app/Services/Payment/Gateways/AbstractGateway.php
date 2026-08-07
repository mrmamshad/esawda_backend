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

    /**
     * Default callback handling. Deliberately conservative: we never mark a
     * transaction successful from a client-supplied payload — a concrete
     * gateway must override this and verify server-side (signature / gateway
     * API + amount match) before setting status to 'success'.
     *
     * @throws \RuntimeException when a client payload claims success.
     */
    public function handleCallback(array $payload): Transaction
    {
        $tx = Transaction::findOrFail($payload['transaction_id'] ?? 0);

        $claimed = strtolower((string) ($payload['status'] ?? 'pending'));
        if ($claimed === 'success') {
            throw new \RuntimeException(
                sprintf('%s::handleCallback must verify server-side before marking success', static::class)
            );
        }

        $tx->status     = $claimed;
        $tx->payment_id = $payload['payment_id'] ?? $tx->payment_id;
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
