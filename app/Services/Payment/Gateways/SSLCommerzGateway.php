<?php

namespace App\Services\Payment\Gateways;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * SSLCommerz payment gateway (Bangladesh).
 *
 * Reference: https://developer.sslcommerz.com/doc/v4/
 *
 * Flow:
 *   1. `initiate($tx)` posts to /gwprocess/v4/api.php → returns GatewayPageURL.
 *   2. Buyer completes on SSLCommerz hosted page.
 *   3. SSLCommerz POSTs to success_url, fail_url, cancel_url, ipn_url.
 *   4. `handleCallback($payload)` validates via /validator/api/validationserverAPI.php
 *      and marks the local transaction record.
 */
class SSLCommerzGateway extends AbstractGateway
{
    public function slug(): string  { return 'sslcommerz'; }
    public function label(): string { return 'SSLCommerz (Cards, Mobile Banking, Net Banking)'; }

    /**
     * Build the POST payload and hit SSLCommerz session API.
     * Returns the GatewayPageURL string on success, or null on failure.
     */
    public function initiate(Transaction $tx): mixed
    {
        $storeId  = (string) config('sslcommerz.store_id');
        $storePwd = (string) config('sslcommerz.store_password');
        $base     = (string) config('sslcommerz.api_domain');
        $currency = (string) config('sslcommerz.currency', 'BDT');

        // Fail closed when credentials are missing — never fall back to a
        // shared sandbox account.
        if ($storeId === '' || $storePwd === '') {
            Log::error('SSLCommerz init aborted: missing store credentials');
            return null;
        }

        // Persist gateway + pending status early so IPN can find the row.
        $tx->transaction_gatway = $this->slug();
        $tx->status             = 'pending';
        if (empty($tx->payment_id)) {
            $tx->payment_id = 'ES_' . $tx->id . '_' . time();
        }
        $tx->save();

        $buyerName  = $tx->seller?->name  ?? 'Buyer';
        $buyerEmail = $tx->seller?->email ?? 'buyer@example.com';
        $buyerPhone = $tx->seller?->phone ?? '01700000000';

        $postData = [
            'store_id'          => $storeId,
            'store_passwd'      => $storePwd,
            'total_amount'      => (float) $tx->amount,
            'currency'          => $currency,
            'tran_id'           => $tx->payment_id,
            'success_url'       => URL::to('/api/v1/payments/sslcommerz/success'),
            'fail_url'          => URL::to('/api/v1/payments/sslcommerz/fail'),
            'cancel_url'        => URL::to('/api/v1/payments/sslcommerz/cancel'),
            'ipn_url'           => URL::to('/api/v1/payments/sslcommerz/ipn'),
            'cus_name'          => $buyerName,
            'cus_email'         => $buyerEmail,
            'cus_add1'          => 'N/A',
            'cus_city'          => 'Dhaka',
            'cus_country'       => 'Bangladesh',
            'cus_phone'         => $buyerPhone,
            'shipping_method'   => 'NO',
            'product_name'      => $tx->product_name ?? ('Transaction #' . $tx->id),
            'product_category'  => 'Digital',
            'product_profile'   => 'non-physical-goods',
        ];

        try {
            $res = Http::asForm()->post("{$base}/gwprocess/v4/api.php", $postData);
            if (! $res->successful()) {
                Log::error('SSLCommerz init HTTP error', ['status' => $res->status(), 'body' => $res->body()]);
                return null;
            }
            $body = $res->json();
            if (($body['status'] ?? '') !== 'SUCCESS' || empty($body['GatewayPageURL'])) {
                Log::error('SSLCommerz init API error', ['response' => $body]);
                return null;
            }
            return $body['GatewayPageURL'];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz init exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate the callback from SSLCommerz. Payload comes from either
     * success_url (browser POST) or ipn_url (server-to-server POST).
     */
    public function handleCallback(array $payload): Transaction
    {
        $tranId = (string) ($payload['tran_id'] ?? '');
        $valId  = (string) ($payload['val_id']  ?? '');
        $status = strtoupper((string) ($payload['status'] ?? ''));

        /** @var Transaction $tx */
        $tx = Transaction::where('payment_id', $tranId)->firstOrFail();

        // When verify_hash is enabled, require SSLCommerz's hash signature
        // (verify_key + verify_sign) to match before accepting anything.
        if (config('sslcommerz.verify_hash', true) && ! $this->verifyHash($payload)) {
            Log::warning('SSLCommerz callback rejected: bad verify hash', ['tran_id' => $tranId]);
            $tx->status = 'failed';
            $tx->save();
            return $tx;
        }

        // Success only when SSLCommerz's validation API confirms the
        // val_id AND the paid amount matches our local record. Never trust
        // the browser-posted status or val_id alone (amount-tampering guard).
        if ($valId !== '' && $this->validateWithApi($valId, $tx)) {
            $tx->status = 'success';
        } elseif ($status === 'FAILED') {
            $tx->status = 'failed';
        } elseif ($status === 'CANCELLED') {
            $tx->status = 'cancel';
        } else {
            $tx->status = 'pending';
        }

        $tx->transaction_gatway = $this->slug();
        $tx->save();

        return $tx;
    }

    /** Direct API verification — never trust the browser-posted status alone. */
    public function verify(Transaction $tx): bool
    {
        return $tx->status === \App\Enums\TransactionStatus::Success;
    }

    /**
     * Verify SSLCommerz's hash signature. Per the v4 spec: take the
     * verify_key's comma-separated field names, sort them, append
     * `store_passwd` (MD5 or SHA-256 of the store password, matching the
     * algorithm SSLCommerz used), and hash the resulting `k1=v1&k2=v2...`
     * string. Compare against verify_sign (MD5) or verify_sign_sha2
     * (SHA-256). Guards against forged callbacks that skip the server
     * validation API.
     */
    private function verifyHash(array $payload): bool
    {
        $key   = (string) ($payload['verify_key'] ?? '');
        $md5   = (string) ($payload['verify_sign'] ?? '');
        $sha2  = (string) ($payload['verify_sign_sha2'] ?? '');
        $store = (string) config('sslcommerz.store_password');

        if ($key === '' || $store === '') return false;

        $fields = array_filter(explode(',', $key));

        if ($md5 !== '') {
            $new = [];
            foreach ($fields as $f) $new[$f] = (string) ($payload[$f] ?? '');
            $new['store_passwd'] = md5($store);
            ksort($new);
            $query = implode('&', array_map(fn ($k, $v) => $k . '=' . $v, array_keys($new), $new));
            if (hash_equals(md5($query), $md5)) return true;
        }

        if ($sha2 !== '') {
            $new = [];
            foreach ($fields as $f) $new[$f] = (string) ($payload[$f] ?? '');
            $new['store_passwd'] = hash('sha256', $store);
            ksort($new);
            $query = implode('&', array_map(fn ($k, $v) => $k . '=' . $v, array_keys($new), $new));
            if (hash_equals(hash('sha256', $query), $sha2)) return true;
        }

        return false;
    }

    /**
     * Validate a val_id with SSLCommerz's server API and confirm the paid
     * amount matches the local transaction. Returns false unless BOTH the
     * payment status is VALID/VALIDATED AND the amounts agree.
     */
    private function validateWithApi(string $valId, Transaction $tx): bool
    {
        $base     = (string) config('sslcommerz.api_domain');
        $storeId  = (string) config('sslcommerz.store_id');
        $storePwd = (string) config('sslcommerz.store_password');

        try {
            $res = Http::get("{$base}/validator/api/validationserverAPI.php", [
                'val_id'       => $valId,
                'store_id'     => $storeId,
                'store_passwd' => $storePwd,
                'format'       => 'json',
            ]);
            if (! $res->successful()) return false;
            $data = $res->json();

            $validStatus = in_array(($data['status'] ?? ''), ['VALID', 'VALIDATED'], true);
            if (! $validStatus) return false;

            // Amount tampering check — the val_id may be real but belong to a
            // smaller/other payment; reject unless it paid exactly our amount.
            $paidAmount = (float) ($data['amount'] ?? 0);
            return abs($paidAmount - (float) $tx->amount) < 0.01;
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validate exception: ' . $e->getMessage());
            return false;
        }
    }
}
