<?php

namespace Tests\Unit\Payment\DGePay;

use App\Services\Payment\DGePay\DGePayClient;
use App\Services\Payment\DGePay\DGePayCrypto;
use App\Services\Payment\DGePay\DGePayException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DGePayClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
        config([
            'dgepay.base_url' => 'https://api-uat.dgepay.net/dipon/v3',
            'dgepay.endpoint_prefix' => 'payment_gateway',
            'dgepay.client_id' => 'test-client',
            'dgepay.client_secret' => '0123456789abcdef',
            'dgepay.api_key' => 'test-api-key',
            'dgepay.content_type' => 'text/plain',
            'dgepay.auth_send_body' => false,
            'dgepay.webview_hosts' => ['dgepay.net'],
        ]);
    }

    public function test_it_authenticates_signs_encrypts_and_caches_the_token(): void
    {
        $payload = ['amount' => 100.0, 'customer_token' => null, 'unique_txn_id' => 'TX-1'];
        $crypto = app(DGePayCrypto::class);

        Http::fake(function (Request $request) use ($payload, $crypto) {
            if (str_ends_with($request->url(), '/authenticate')) {
                $this->assertStringStartsWith('Basic ', $request->header('Authorization')[0] ?? '');

                return Http::response(['success' => 1, 'data' => ['access_token' => 'jwt-test', 'expiry_time' => 3600]]);
            }

            $this->assertSame('Bearer jwt-test', $request->header('Authorization')[0] ?? null);
            $this->assertNotEmpty($request->header('Signature')[0] ?? null);
            $this->assertSame($payload, json_decode($crypto->decrypt($request->body(), '0123456789abcdef'), true));

            return Http::response(['success' => 1, 'data' => ['webview_url' => 'https://checkout.dgepay.net/pay/1']]);
        });

        $client = app(DGePayClient::class);
        $this->assertSame(
            'https://checkout.dgepay.net/pay/1',
            data_get($client->initiate($payload), 'data.webview_url'),
        );
        $client->initiate($payload);

        Http::assertSentCount(3); // one authentication + two encrypted calls
    }

    public function test_it_decrypts_return_data_and_rejects_unsafe_webview_hosts(): void
    {
        $client = app(DGePayClient::class);
        $encrypted = app(DGePayCrypto::class)->encrypt(
            json_encode(['unique_txn_id' => 'TX-1']),
            '0123456789abcdef',
        );

        $this->assertSame('TX-1', $client->decryptReturn($encrypted)['unique_txn_id']);

        $this->expectException(DGePayException::class);
        $client->assertSafeWebviewUrl('https://dgepay.net.attacker.example/pay');
    }

    public function test_it_refreshes_the_token_once_after_an_unauthorised_response(): void
    {
        Http::fakeSequence()
            ->push(['data' => ['access_token' => 'old-token', 'expiry_time' => 3600]])
            ->push([], 401)
            ->push(['data' => ['access_token' => 'new-token', 'expiry_time' => 3600]])
            ->push(['data' => ['webview_url' => 'https://checkout.dgepay.net/pay/2']]);

        $response = app(DGePayClient::class)->initiate(['amount' => 10.0]);

        $this->assertSame('https://checkout.dgepay.net/pay/2', data_get($response, 'data.webview_url'));
        Http::assertSentCount(4);
    }
}
