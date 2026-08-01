<?php

namespace App\Services\Payment\Gateways;

use App\Models\Transaction;

/**
 * Legacy: includes/payments/wire_transfer.
 * No online API — buyer sees bank details, admin marks transaction paid manually.
 */
class WireTransferGateway extends AbstractGateway
{
    public function slug(): string  { return 'wire_transfer'; }
    public function label(): string { return 'Wire Transfer / Bank Deposit'; }

    public function initiate(Transaction $tx): mixed
    {
        $tx->transaction_gatway = $this->slug();
        $tx->status = 'pending';
        $tx->save();
        // Redirect to a public page that shows bank details.
        return route('payment', [
            'access_token' => $tx->id,
            'i'            => $this->slug(),
            'status'       => 'awaiting_transfer',
        ]);
    }

    public function handleCallback(array $payload): Transaction
    {
        $tx = Transaction::findOrFail((int) ($payload['tx'] ?? 0));
        if (($payload['result'] ?? '') === 'confirmed') {
            $tx->status = 'success';
        }
        $tx->save();
        return $tx;
    }
}
