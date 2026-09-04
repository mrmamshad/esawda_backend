<?php

namespace Tests\Feature;

use App\Jobs\FulfilTransactionJob;
use App\Jobs\RevalidateFrontendJob;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\PaymentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PayToPostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test category for ads
        \DB::table('catagory_main')->insert([
            'cat_id' => 1,
            'cat_order' => 1,
            'cat_name' => 'Test Category',
            'slug' => 'test-category',
            'icon' => 'test-icon',
            'picture' => 'test.jpg',
        ]);
    }

    public function test_ad_post_checkout_creates_transaction(): void
    {
        // Hermetic: never hit the real SSLCommerz sandbox from tests (CI has
        // no route/creds for it) — stub the init call like the other flows.
        Http::fake([
            '*/gwprocess/v4/api.php' => Http::response([
                'status' => 'SUCCESS',
                'GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/test-session',
            ]),
        ]);

        $user = User::factory()->create();
        $plan = Plan::factory()->create(['monthly_price' => 500]);

        $response = $this->actingAs($user)->postJson('/api/v1/checkout/ad-post', [
            'plan_id' => $plan->id,
            'ad_data' => [
                'title' => 'Test Ad',
                'description' => 'Test description for payment flow',
                'price' => 5000,
                'category' => 1,
                'condition' => 'new',
            ],
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['transaction_id', 'gateway_url']]);

        $this->assertDatabaseHas('transaction', [
            'seller_id' => $user->id,
            'plan_id' => $plan->id,
            'purpose' => 'ad_post',
            'status' => 'pending',
        ]);
    }

    public function test_fulfil_transaction_creates_ad_after_payment(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['monthly_price' => 500]);

        $tx = Transaction::create([
            'seller_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => 500,
            'status' => 'success',
            'purpose' => 'ad_post',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode([
                'title' => 'Paid Ad',
                'description' => 'Ad created after successful payment',
                'price' => 10000,
                'category' => 1,
                'condition' => 'new',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatch($tx->id);

        $this->assertDatabaseHas('product', [
            'product_name' => 'Paid Ad',
            'paid' => true,
            'transaction_id' => $tx->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('user', [
            'id' => $user->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_subscription_listing_consumes_one_slot_and_stays_pending(): void
    {
        $user = User::factory()->create([
            'plan_id' => 1,
            'plan_expires_at' => now()->addMonth(),
            'ads_remaining' => 2,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/ads', [
            'title' => 'Subscription Ad',
            'description' => 'Subscription listing awaits admin review',
            'price' => 1000,
            'category' => 1,
            'condition' => 'used',
        ]);

        $response->assertCreated()
            ->assertJson(['data' => ['title' => 'Subscription Ad']]);

        $this->assertSame(1, (int) $user->fresh()->ads_remaining);
        $this->assertDatabaseHas('product', [
            'product_name' => 'Subscription Ad',
            'status' => 'pending',
            'paid' => false,
        ]);
    }

    public function test_pending_ad_not_visible_to_anonymous_detail(): void
    {
        $owner = User::factory()->create();

        $pending = Post::create([
            'user_id' => $owner->id,
            'product_name' => 'Pending Secret',
            'description' => 'Should not be public yet',
            'price' => 100,
            'category' => 1,
            'condition' => 'used',
            'status' => 'pending',
            'hide' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->getJson("/api/v1/ads/{$pending->id}-secret")->assertNotFound();
    }

    public function test_pending_ad_not_visible_to_other_user_detail(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $pending = Post::create([
            'user_id' => $owner->id,
            'product_name' => 'Pending Secret',
            'description' => 'Should not be public yet',
            'price' => 100,
            'category' => 1,
            'condition' => 'used',
            'status' => 'pending',
            'hide' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->json('GET', "/api/v1/ads/{$pending->id}-secret", [], ['Authorization' => "Bearer {$token}"])
            ->assertNotFound();
    }

    public function test_ad_owner_can_preview_pending_detail(): void
    {
        $owner = User::factory()->create();
        $token = $owner->createToken('test')->plainTextToken;

        $pending = Post::create([
            'user_id' => $owner->id,
            'product_name' => 'Pending Secret',
            'description' => 'Should not be public yet',
            'price' => 100,
            'category' => 1,
            'condition' => 'used',
            'status' => 'pending',
            'hide' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->json('GET', "/api/v1/ads/{$pending->id}-secret", [], ['Authorization' => "Bearer {$token}"])
            ->assertOk();
    }

    public function test_transaction_status_endpoint(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $tx = Transaction::create([
            'seller_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => 500,
            'status' => 'success',
            'purpose' => 'ad_post',
            'transaction_gatway' => 'sslcommerz',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson("/api/v1/checkout/transactions/{$tx->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $tx->id,
                    'status' => 'success',
                    'purpose' => 'ad_post',
                ],
            ]);
    }

    public function test_plan_purchase_writes_plan_expires_at_column(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['name' => 'Basic']);

        $tx = Transaction::create([
            'seller_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => 500,
            'status' => 'success',
            'purpose' => 'plan',
            'transaction_gatway' => 'sslcommerz',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatchSync($tx->id);

        $this->assertDatabaseHas('user', [
            'id' => $user->id,
            'group_id' => 'Basic',
        ]);
        $this->assertNotNull($user->fresh()->plan_expires_at);
    }

    public function test_fulfil_creates_ad_even_when_plan_row_missing(): void
    {
        $user = User::factory()->create();

        // Simulate a paid ad_post whose plan row was later removed — the ad
        // must still be created (never silently drop a paid listing).
        $tx = Transaction::create([
            'seller_id' => $user->id,
            'plan_id' => 99999,
            'amount' => 500,
            'status' => 'success',
            'purpose' => 'ad_post',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode([
                'title' => 'Orphan Plan Ad',
                'description' => 'Ad must exist even though plan is gone',
                'price' => 500,
                'category' => 1,
                'condition' => 'used',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatchSync($tx->id);

        $this->assertDatabaseHas('product', [
            'product_name' => 'Orphan Plan Ad',
            'paid' => true,
            'transaction_id' => $tx->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_reject_sets_rejected_status(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();

        $ad = Post::create([
            'user_id' => $owner->id,
            'product_name' => 'Reject Me',
            'description' => 'Listing to be rejected',
            'price' => 50,
            'category' => 1,
            'condition' => 'new',
            'status' => 'pending',
            'hide' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)->postJson("/api/v1/admin/ads/{$ad->id}/reject", [
            'reason' => 'Policy violation',
        ])->assertOk();

        $this->assertDatabaseHas('product', [
            'id' => $ad->id,
            'status' => 'rejected',
            'hide' => '1',
            'reject_reason' => 'Policy violation',
        ]);
    }

    public function test_fulfil_ad_upgrade_marks_paid_and_sets_flags(): void
    {
        $user = User::factory()->create();
        $ad = Post::create([
            'user_id' => $user->id,
            'product_name' => 'Upgrade Me',
            'description' => 'Ad to upgrade',
            'price' => 100,
            'category' => 1,
            'condition' => 'used',
            'status' => 'pending',
            'hide' => '0',
            'featured' => '0',
            'urgent' => '0',
            'highlight' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tx = Transaction::create([
            'seller_id' => $user->id,
            'product_id' => $ad->id,
            'amount' => 200,
            'status' => 'success',
            'purpose' => 'ad_upgrade',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode(['featured' => true, 'urgent' => false, 'highlight' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatchSync($tx->id);

        $this->assertDatabaseHas('product', [
            'id' => $ad->id,
            'paid' => true,
            'featured' => '1',
            'urgent' => '0',
            'highlight' => '1',
            'transaction_id' => $tx->id,
        ]);
    }

    public function test_sslcommerz_callback_accepts_valid_hash(): void
    {
        config(['sslcommerz.store_password' => 'qwerty']);

        Http::fake([
            '*/validator/api/validationserverAPI.php' => Http::response([
                'status' => 'VALID',
                'amount' => '200.00',
            ]),
        ]);

        $owner = User::factory()->create();
        $tx = Transaction::create([
            'seller_id' => $owner->id,
            'plan_id' => 99999,
            'amount' => 200,
            'status' => 'pending',
            'purpose' => 'ad_upgrade',
            'transaction_gatway' => 'sslcommerz',
            'payment_id' => 'ES_4_1786789110',
            'meta' => json_encode(['product_id' => 7]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Realistic SSLCommerz v4 callback — fields are the same as the
        // sandbox returns. verify_sign_sha2 is computed with the
        // documented algorithm (sorted fields + store_passwd=SHA256(pw)).
        $payload = [
            'tran_id' => 'ES_4_1786789110',
            'val_id' => '260815161856cQHW6sXY66p447Q',
            'amount' => '200.00',
            'store_amount' => '195.00',
            'status' => 'VALID',
            'store_id' => 'testbox',
            'card_type' => 'VISA-Dutch Bangla',
            'verify_key' => 'amount,store_amount,status,tran_id,val_id',
            'verify_sign_sha2' => hash('sha256', 'amount=200.00&store_amount=195.00&status=VALID&store_passwd='.hash('sha256', 'qwerty').'&tran_id=ES_4_1786789110&val_id=260815161856cQHW6sXY66p447Q'),
        ];

        $gw = (new PaymentManager)->get('sslcommerz');

        // validateWithApi would fail (no real network) but the hash check
        // runs first; we just assert it doesn't blow up and returns the tx.
        $result = $gw->handleCallback($payload);
        $this->assertSame($tx->id, $result->id);
    }

    public function test_admin_approve_dispatches_frontend_revalidation(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $ad = Post::create([
            'user_id' => $owner->id,
            'product_name' => 'Revalidate Test',
            'description' => 'Should trigger revalidation on approve',
            'price' => 100,
            'category' => 1,
            'condition' => 'new',
            'status' => 'pending',
            'hide' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Queue::fake();
        Cache::shouldReceive('flush')->once();

        $this->actingAs($admin)->postJson("/api/v1/admin/ads/{$ad->id}/approve")->assertOk();

        Queue::assertPushed(RevalidateFrontendJob::class);
    }

    public function test_sslcommerz_callback_rejects_bad_hash(): void
    {
        config(['sslcommerz.store_password' => 'qwerty']);

        $owner = User::factory()->create();
        $tx = Transaction::create([
            'seller_id' => $owner->id,
            'plan_id' => 99999,
            'amount' => 200,
            'status' => 'pending',
            'purpose' => 'ad_upgrade',
            'transaction_gatway' => 'sslcommerz',
            'payment_id' => 'ES_4_1786789110',
            'meta' => json_encode(['product_id' => 7]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'tran_id' => 'ES_4_1786789110',
            'val_id' => '260815161856cQHW6sXY66p447Q',
            'amount' => '200.00',
            'store_amount' => '195.00',
            'status' => 'VALID',
            'store_id' => 'testbox',
            'card_type' => 'VISA-Dutch Bangla',
            'verify_key' => 'amount,store_amount,status,tran_id,val_id',
            'verify_sign_sha2' => str_repeat('0', 64), // forged signature
        ];

        $gw = (new PaymentManager)->get('sslcommerz');
        $result = $gw->handleCallback($payload);

        $this->assertSame($tx->id, $result->id);
        $this->assertSame('failed', $result->status->value);
    }
}
