<?php

namespace App\Services\Payment\Gateways;

use App\Models\Transaction;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

/**
 * Legacy: includes/payments/paypal — ported to Laravel service.
 * Uses srmklive/paypal SDK. Config keys read from config/quickad.php →
 * gateways.paypal.{client_id,secret,mode}.
 */
class PaypalGateway extends AbstractGateway
{
    public function slug(): string  { return 'paypal'; }
    public function label(): string { return 'PayPal'; }

    private function client(): PayPalClient
    {
        $c = new PayPalClient([
            'mode' => $this->conf('mode', 'sandbox'),
            $this->conf('mode', 'sandbox') => [
                'client_id'     => $this->conf('client_id', ''),
                'client_secret' => $this->conf('secret', ''),
                'app_id'        => 'APP-80W284485P519543T',
            ],
            'currency' => $this->conf('currency', 'USD'),
            'notify_url' => '',
            'locale' => 'en_US',
            'validate_ssl' => true,
        ]);
        $c->getAccessToken();
        return $c;
    }

    public function initiate(Transaction $tx): mixed
    {
        // If credentials not configured, gracefully fall back so the site keeps working.
        if (! $this->conf('client_id')) {
            $tx->status = 'pending';
            $tx->transaction_gatway = $this->slug();
            $tx->save();
            return route('payment', ['access_token' => $tx->id, 'i' => $this->slug(), 'status' => 'pending']);
        }
        $client = $this->client();
        $order = $client->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $tx->id,
                'amount' => [
                    'currency_code' => $this->conf('currency', 'USD'),
                    'value' => number_format((float) $tx->amount, 2, '.', ''),
                ],
                'description' => $tx->product_name ?? ('Transaction #' . $tx->id),
            ]],
            'application_context' => [
                'return_url' => route('payment.ipn', ['i' => 'paypal']) . '?tx=' . $tx->id . '&result=success',
                'cancel_url' => route('payment.ipn', ['i' => 'paypal']) . '?tx=' . $tx->id . '&result=cancel',
            ],
        ]);
        // Persist PayPal order id + redirect user to approval url
        $tx->payment_id = $order['id'] ?? null;
        $tx->status     = 'pending';
        $tx->transaction_gatway = $this->slug();
        $tx->save();
        foreach ($order['links'] ?? [] as $link) {
            if (($link['rel'] ?? '') === 'approve') return $link['href'];
        }
        return route('payment', ['access_token' => $tx->id, 'i' => 'paypal']);
    }

    public function handleCallback(array $payload): Transaction
    {
        $tx = Transaction::findOrFail((int) ($payload['tx'] ?? 0));
        if (($payload['result'] ?? '') === 'success' && $this->conf('client_id')) {
            try {
                $capture = $this->client()->capturePaymentOrder($tx->payment_id);
                $tx->status = ($capture['status'] ?? 'FAILED') === 'COMPLETED' ? 'success' : 'failed';
            } catch (\Throwable) {
                $tx->status = 'failed';
            }
        } elseif (($payload['result'] ?? '') === 'success') {
            // No client_id configured → cannot verify via PayPal API. Fail
            // closed; never mark success from an unverified payload.
            $tx->status = 'failed';
        } else {
            $tx->status = 'cancel';
        }
        $tx->save();
        return $tx;
    }
}
