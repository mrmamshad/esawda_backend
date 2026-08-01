<?php

namespace App\Services\Payment\Gateways;

use App\Models\Transaction;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

/**
 * Legacy: includes/payments/stripe — ported to Laravel.
 * Uses Stripe Checkout session API.
 */
class StripeGateway extends AbstractGateway
{
    public function slug(): string  { return 'stripe'; }
    public function label(): string { return 'Stripe'; }

    public function initiate(Transaction $tx): mixed
    {
        $secret = $this->conf('secret');
        if (! $secret) {
            $tx->status = 'pending';
            $tx->transaction_gatway = $this->slug();
            $tx->save();
            return route('payment', ['access_token' => $tx->id, 'i' => $this->slug(), 'status' => 'pending']);
        }
        Stripe::setApiKey($secret);
        $session = StripeSession::create([
            'mode' => 'payment',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => $this->conf('currency', 'usd'),
                    'unit_amount' => (int) round(((float) $tx->amount) * 100),
                    'product_data' => ['name' => $tx->product_name ?? ('Transaction #' . $tx->id)],
                ],
            ]],
            'success_url' => route('payment.ipn', ['i' => 'stripe']) . '?tx=' . $tx->id . '&result=success&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('payment.ipn', ['i' => 'stripe']) . '?tx=' . $tx->id . '&result=cancel',
            'metadata' => ['transaction_id' => (string) $tx->id],
        ]);
        $tx->payment_id = $session->id;
        $tx->status     = 'pending';
        $tx->transaction_gatway = $this->slug();
        $tx->save();
        return $session->url;
    }

    public function handleCallback(array $payload): Transaction
    {
        $tx = Transaction::findOrFail((int) ($payload['tx'] ?? 0));
        if (($payload['result'] ?? '') === 'success') {
            $secret = $this->conf('secret');
            if ($secret) {
                try {
                    Stripe::setApiKey($secret);
                    $session = StripeSession::retrieve($payload['session_id'] ?? $tx->payment_id);
                    $tx->status = ($session->payment_status ?? '') === 'paid' ? 'success' : 'pending';
                } catch (\Throwable) {
                    $tx->status = 'failed';
                }
            } else {
                $tx->status = 'success';
            }
        } else {
            $tx->status = 'cancel';
        }
        $tx->save();
        return $tx;
    }
}
