<?php

namespace Tests\Feature;

use App\Jobs\FulfilTransactionJob;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'sslcommerz.store_id' => 'testbox',
            'sslcommerz.store_password' => 'qwerty',
            'sslcommerz.api_domain' => 'https://sandbox.sslcommerz.com',
            'sslcommerz.frontend_url' => 'http://localhost:3000',
        ]);
    }

    public function test_plan_checkout_persists_billing_cadence(): void
    {
        Http::fake([
            '*/gwprocess/v4/api.php' => Http::response([
                'status' => 'SUCCESS',
                'GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/test-session',
            ]),
        ]);

        $user = User::factory()->create(['user_type' => 'seller']);
        $plan = Plan::factory()->create([
            'monthly_price' => 500,
            'annual_price' => 5000,
        ]);

        $response = $this->actingAs($user)->postJson("/api/v1/checkout/plan/{$plan->id}", [
            'cadence' => 'annual',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.gateway_url', 'https://sandbox.sslcommerz.com/EasyCheckOut/test-session');

        $transaction = Transaction::findOrFail($response->json('data.transaction_id'));
        $this->assertSame('plan', $transaction->purpose);
        $this->assertSame(5000.0, (float) $transaction->amount);
        $this->assertSame('annual', json_decode((string) $transaction->meta, true)['cadence']);
    }

    public function test_annual_plan_fulfilment_is_idempotent(): void
    {
        $user = User::factory()->create(['user_type' => 'seller']);
        $plan = Plan::factory()->create([
            'name' => 'Business',
            'settings' => json_encode(['ads_limit' => 40]),
        ]);
        $transaction = Transaction::create([
            'seller_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => 5000,
            'status' => 'success',
            'purpose' => 'plan',
            'meta' => json_encode(['cadence' => 'annual']),
            'transaction_gatway' => 'sslcommerz',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatchSync($transaction->id);
        $firstExpiry = $user->fresh()->plan_expires_at;

        $this->assertSame($plan->id, (int) $user->fresh()->plan_id);
        $this->assertSame(40, (int) $user->fresh()->ads_remaining);
        $this->assertTrue($firstExpiry->greaterThan(now()->addMonths(11)));
        $this->assertNotNull($transaction->fresh()->fulfilled_at);

        FulfilTransactionJob::dispatchSync($transaction->id);
        $this->assertTrue($user->fresh()->plan_expires_at->equalTo($firstExpiry));
    }

    public function test_success_callback_activates_plan_without_queue_worker(): void
    {
        Http::fake([
            '*/validator/api/validationserverAPI.php*' => Http::response([
                'status' => 'VALID',
                'amount' => '500.00',
            ]),
        ]);

        $user = User::factory()->create(['user_type' => 'seller']);
        $plan = Plan::factory()->create([
            'name' => 'Basic Plan',
            'settings' => json_encode(['ads_limit' => 10]),
        ]);
        $transaction = Transaction::create([
            'seller_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => 500,
            'status' => 'pending',
            'purpose' => 'plan',
            'meta' => json_encode(['cadence' => 'monthly']),
            'transaction_gatway' => 'sslcommerz',
            'payment_id' => 'ES_CALLBACK_TEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'tran_id' => 'ES_CALLBACK_TEST',
            'val_id' => 'VALIDATION_ID',
            'amount' => '500.00',
            'store_amount' => '487.50',
            'status' => 'VALID',
            'verify_key' => 'amount,store_amount,status,tran_id,val_id',
        ];
        $payload['verify_sign_sha2'] = hash(
            'sha256',
            'amount=500.00&status=VALID&store_amount=487.50&store_passwd='.hash('sha256', 'qwerty').'&tran_id=ES_CALLBACK_TEST&val_id=VALIDATION_ID',
        );

        $this->post('/api/v1/payments/sslcommerz/success', $payload)
            ->assertRedirect('http://localhost:3000/membership/success?tx='.$transaction->id.'&status=success');

        $this->assertSame('success', $transaction->fresh()->status->value);
        $this->assertNotNull($transaction->fresh()->fulfilled_at);
        $this->assertSame($plan->id, (int) $user->fresh()->plan_id);
        $this->assertTrue($user->fresh()->plan_expires_at->isFuture());
        $this->assertSame(10, (int) $user->fresh()->ads_remaining);
    }
}
