<?php

namespace Tests\Feature;

use App\Models\PaymentEvent;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\DGePay\DGePayCrypto;
use App\Services\Payment\DGePay\DGePayException;
use App\Services\Payment\Gateways\DGePayGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DGePayPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
        config([
            'payments.primary' => 'dgepay',
            'payments.gateways.dgepay.accept_new' => true,
            'payments.gateways.dgepay.accept_callbacks' => true,
            'dgepay.base_url' => 'https://api-uat.dgepay.net/dipon/v3',
            'dgepay.endpoint_prefix' => 'payment_gateway',
            'dgepay.client_id' => 'test-client',
            'dgepay.client_secret' => '0123456789abcdef',
            'dgepay.api_key' => 'test-api-key',
            'dgepay.webview_hosts' => ['dgepay.net'],
            'quickad.frontend.url' => 'http://localhost:3000',
        ]);
    }

    public function test_plan_checkout_and_verified_return_fulfil_the_transaction(): void
    {
        $gatewayReference = null;
        Http::fake(function ($request) use (&$gatewayReference) {
            if (str_ends_with($request->url(), '/authenticate')) {
                return Http::response(['success' => 1, 'data' => ['access_token' => 'jwt-test', 'expiry_time' => 3600]]);
            }
            if (str_ends_with($request->url(), '/initiate_payment')) {
                return Http::response(['success' => 1, 'data' => ['webview_url' => 'https://checkout.dgepay.net/pay/session']]);
            }

            return Http::response([
                'success' => 1,
                'data' => [
                    'unique_txn_id' => $gatewayReference,
                    'status_code' => '3',
                    'amount' => 500,
                    'txn_id' => 'gateway-id-1',
                    'txn_number' => 'gateway-number-1',
                    'payment_method' => '2002',
                    'message' => 'Success',
                ],
            ]);
        });

        $user = User::factory()->create(['user_type' => 'seller', 'phone' => '01700000000']);
        $plan = Plan::factory()->create(['monthly_price' => 500, 'settings' => json_encode(['ads_limit' => 10])]);

        $checkout = $this->actingAs($user)
            ->withHeader('Idempotency-Key', 'checkout-plan-1')
            ->postJson("/api/v1/checkout/plan/{$plan->id}", [
                'cadence' => 'monthly',
                'policies_accepted' => true,
                'payment_phone' => '01700000000',
            ])
            ->assertOk()
            ->assertJsonPath('data.gateway_url', 'https://checkout.dgepay.net/pay/session');

        $tx = Transaction::findOrFail($checkout->json('data.transaction_id'));
        $this->assertSame('dgepay', $tx->transaction_gatway);
        $this->assertSame(50000, $tx->amount_minor);
        $this->assertNotNull($tx->policy_accepted_at);
        $this->assertStringStartsWith('ES-', (string) $tx->payment_id);

        $gatewayReference = (string) $tx->payment_id;

        $returnData = app(DGePayCrypto::class)->encrypt(
            json_encode(['unique_txn_id' => $tx->payment_id]),
            '0123456789abcdef',
        );

        $this->get('/api/v1/payments/dgepay/return?data='.urlencode($returnData))
            ->assertRedirect("http://localhost:3000/payment/result?transaction_id={$tx->id}&status=success");

        $this->assertSame('success', $tx->fresh()->status->value);
        $this->assertNotNull($tx->fresh()->fulfilled_at);
        $this->assertSame($plan->id, (int) $user->fresh()->plan_id);
        $this->assertSame(1, PaymentEvent::query()->count());
    }

    public function test_dgepay_checkout_requires_policy_acceptance_and_idempotency_key(): void
    {
        $user = User::factory()->create(['user_type' => 'seller']);
        $plan = Plan::factory()->create(['monthly_price' => 500]);

        $this->actingAs($user)
            ->postJson("/api/v1/checkout/plan/{$plan->id}")
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');

        $this->assertDatabaseCount('transaction', 0);
    }

    public function test_production_uat_checkout_is_restricted_to_allowlisted_users(): void
    {
        $this->app['env'] = 'production';
        config(['dgepay.uat_allowed_user_ids' => [999999]]);

        $user = User::factory()->create(['user_type' => 'seller']);
        $plan = Plan::factory()->create(['monthly_price' => 500]);

        $this->actingAs($user)
            ->withHeader('Idempotency-Key', 'restricted-uat-checkout')
            ->postJson("/api/v1/checkout/plan/{$plan->id}", [
                'policies_accepted' => true,
                'payment_phone' => '01700000000',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('transaction', 0);
    }

    public function test_amount_mismatch_never_marks_transaction_successful(): void
    {
        $tx = Transaction::create([
            'seller_id' => User::factory()->create()->id,
            'amount' => 500,
            'amount_minor' => 50000,
            'currency' => 'BDT',
            'transaction_gatway' => 'dgepay',
            'payment_id' => 'ES-TEST-MISMATCH',
            'status' => 'pending',
            'purpose' => 'plan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Http::fakeSequence()
            ->push(['data' => ['access_token' => 'jwt-test', 'expiry_time' => 3600]])
            ->push(['data' => [
                'unique_txn_id' => $tx->payment_id,
                'status_code' => '3',
                'amount' => 1,
            ]]);

        $this->expectException(DGePayException::class);
        app(DGePayGateway::class)->verify($tx);
    }
}
