<?php

namespace App\Services\Payment\DGePay;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JsonException;

class DGePayClient
{
    public function __construct(
        private readonly DGePayCanonicalizer $canonicalizer,
        private readonly DGePayCrypto $crypto,
    ) {}

    public function initiate(array $payload): array
    {
        return $this->encryptedRequest('initiate_payment', $payload);
    }

    public function checkStatus(string $reference): array
    {
        return $this->encryptedRequest('check_transaction_status', ['unique_txn_id' => $reference]);
    }

    public function signature(array $payload): string
    {
        $apiKey = $this->credential('api_key');

        return base64_encode(hash_hmac('sha256', $this->canonicalizer->checksum($payload), $apiKey, true));
    }

    public function decryptReturn(string $data): array
    {
        if (strlen($data) > (int) config('dgepay.return_data_max_bytes', 16384)) {
            throw new DGePayException('DGePay return data is too large.');
        }

        $encoded = preg_replace('/\s+/', '', str_replace(' ', '+', trim($data))) ?? '';
        if ($encoded === '' || !preg_match('/^[A-Za-z0-9+\/]*={0,2}$/', $encoded)) {
            throw new DGePayException('DGePay return data has an invalid format.');
        }
        $remainder = strlen($encoded) % 4;
        if ($remainder !== 0) {
            $encoded .= str_repeat('=', 4 - $remainder);
        }

        try {
            $decoded = json_decode(
                $this->crypto->decrypt($encoded, $this->credential('client_secret')),
                true,
                32,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new DGePayException('DGePay return data is not valid JSON.', previous: $e);
        }

        if (!is_array($decoded) || array_is_list($decoded)) {
            throw new DGePayException('DGePay return data has an invalid structure.');
        }

        return $decoded;
    }

    public function assertSafeWebviewUrl(string $url): string
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (($parts['scheme'] ?? '') !== 'https' || $host === '') {
            throw new DGePayException('DGePay returned a non-HTTPS payment URL.');
        }

        foreach ((array) config('dgepay.webview_hosts', []) as $allowed) {
            $allowed = strtolower(ltrim((string) $allowed, '.'));
            if ($allowed !== '' && ($host === $allowed || str_ends_with($host, '.'.$allowed))) {
                return $url;
            }
        }

        throw new DGePayException('DGePay returned an unapproved payment URL.');
    }

    private function encryptedRequest(string $endpoint, array $payload, bool $retryAuth = true): array
    {
        try {
            $json = json_encode(
                $payload,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new DGePayException('DGePay payload could not be encoded.', previous: $e);
        }

        $encrypted = $this->crypto->encrypt($json, $this->credential('client_secret'));
        $response = $this->request()
            ->withToken($this->accessToken())
            ->withHeaders(['Signature' => $this->signature($payload)])
            ->withBody($encrypted, (string) config('dgepay.content_type', 'text/plain'))
            ->post($this->endpoint($endpoint));

        if ($response->status() === 401 && $retryAuth) {
            Cache::forget($this->tokenCacheKey());

            return $this->encryptedRequest($endpoint, $payload, false);
        }

        return $this->decodeResponse($response, $endpoint);
    }

    private function accessToken(): string
    {
        if ($token = Cache::get($this->tokenCacheKey())) {
            return (string) $token;
        }

        return Cache::lock($this->tokenCacheKey().'.lock', 10)->block(5, function (): string {
            if ($token = Cache::get($this->tokenCacheKey())) {
                return (string) $token;
            }

            $request = $this->request()
                ->acceptJson()
                ->withBasicAuth($this->credential('client_id'), $this->credential('client_secret'));
            $response = config('dgepay.auth_send_body', false)
                ? $request->post($this->endpoint('authenticate'), [
                    'client_id' => $this->credential('client_id'),
                    'client_secret' => $this->credential('client_secret'),
                ])
                : $request->send('POST', $this->endpoint('authenticate'));
            $body = $this->decodeResponse($response, 'authenticate');
            $token = (string) data_get($body, 'data.access_token', '');
            if ($token === '') {
                throw new DGePayException('DGePay authentication response did not contain an access token.');
            }

            Cache::put($this->tokenCacheKey(), $token, $this->tokenTtl(data_get($body, 'data.expiry_time')));

            return $token;
        });
    }

    private function request(): PendingRequest
    {
        return Http::connectTimeout((int) config('dgepay.connect_timeout', 5))
            ->timeout((int) config('dgepay.request_timeout', 20))
            ->acceptJson();
    }

    private function decodeResponse(Response $response, string $operation): array
    {
        if (!$response->successful()) {
            throw new DGePayException("DGePay {$operation} failed with HTTP {$response->status()}.");
        }
        $body = $response->json();
        if (!is_array($body)) {
            throw new DGePayException("DGePay {$operation} returned malformed JSON.");
        }
        if (array_key_exists('success', $body) && !in_array($body['success'], [1, '1', true], true)) {
            $gatewayMessage = data_get($body, 'message') ?? data_get($body, 'error.message');
            $suffix = is_scalar($gatewayMessage) && trim((string) $gatewayMessage) !== ''
                ? ': '.Str::limit(strip_tags((string) $gatewayMessage), 200, '')
                : '';
            throw new DGePayException("DGePay {$operation} was rejected{$suffix}.");
        }

        return $body;
    }

    private function endpoint(string $operation): string
    {
        $base = rtrim((string) config('dgepay.base_url'), '/');
        if (!str_starts_with($base, 'https://')) {
            throw new DGePayException('DGePay base URL must use HTTPS.');
        }
        $prefix = trim((string) config('dgepay.endpoint_prefix', 'payment_gateway'), '/');
        if ($prefix !== '' && !str_ends_with($base, '/'.$prefix)) {
            $base .= '/'.$prefix;
        }

        return $base.'/'.ltrim($operation, '/');
    }

    private function credential(string $key): string
    {
        $value = (string) config("dgepay.{$key}");
        if ($value === '') {
            throw new DGePayException("DGePay {$key} is not configured.");
        }

        return $value;
    }

    private function tokenCacheKey(): string
    {
        return 'dgepay.token.'.hash('sha256', (string) config('dgepay.base_url').'|'.(string) config('dgepay.client_id'));
    }

    private function tokenTtl(mixed $expiry): int
    {
        $default = max(60, (int) config('dgepay.token_default_ttl', 3000));
        try {
            if (is_numeric($expiry)) {
                $number = (int) $expiry;
                $ttl = $number > time() ? $number - time() : $number;
            } elseif (is_string($expiry) && trim($expiry) !== '') {
                $ttl = CarbonImmutable::parse($expiry)->timestamp - time();
            } else {
                $ttl = $default;
            }
        } catch (\Throwable) {
            $ttl = $default;
        }

        return max(60, min(86400, $ttl - 60));
    }
}
