<?php

namespace Tests\Feature;

use App\Jobs\FulfilTransactionJob;
use App\Models\Admin;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ListingPaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('catagory_main')->insert([
            'cat_id' => 1,
            'cat_order' => 1,
            'cat_name' => 'Electronics',
            'slug' => 'electronics',
            'icon' => 'phone',
            'picture' => 'electronics.jpg',
        ]);

        config([
            'sslcommerz.store_id' => 'testbox',
            'sslcommerz.store_password' => 'qwerty',
            'sslcommerz.api_domain' => 'https://sandbox.sslcommerz.com',
        ]);
    }

    public function test_paid_listing_preserves_quota_and_waits_for_admin_approval(): void
    {
        Http::fake([
            '*/gwprocess/v4/api.php' => Http::response([
                'status' => 'SUCCESS',
                'GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/paid-listing',
            ]),
        ]);

        $seller = User::factory()->create([
            'user_type' => 'seller',
            'plan_expires_at' => now()->addMonth(),
            'ads_remaining' => 5,
        ]);

        $response = $this->actingAs($seller)->postJson('/api/v1/checkout/paid-listing', [
            'title' => 'Paid Review Product',
            'description' => 'A complete paid listing that requires admin approval.',
            'price' => 2500,
            'category' => 1,
            'condition' => 'new',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.gateway_url', 'https://sandbox.sslcommerz.com/EasyCheckOut/paid-listing');

        $this->assertSame(5, (int) $seller->fresh()->ads_remaining);
        $post = Post::findOrFail($response->json('data.post_id'));
        $transaction = Transaction::findOrFail($response->json('data.transaction_id'));

        $this->assertSame('draft', $post->status->value);
        $this->assertSame('1', (string) $post->hide);
        $this->assertFalse((bool) $post->paid);
        $this->assertSame('paid_listing', $transaction->purpose);

        $transaction->forceFill(['status' => 'success'])->save();
        FulfilTransactionJob::dispatchSync($transaction->id);

        $post->refresh();
        $this->assertSame(5, (int) $seller->fresh()->ads_remaining);
        $this->assertSame('pending', $post->status->value);
        $this->assertSame('0', (string) $post->hide);
        $this->assertTrue((bool) $post->paid);
        $this->assertNotNull($transaction->fresh()->fulfilled_at);

        $this->getJson('/api/v1/ads')
            ->assertOk()
            ->assertJsonMissing(['title' => 'Paid Review Product']);

        $adminUser = User::factory()->create([
            'username' => 'review-admin',
            'email' => 'review-admin@example.test',
        ]);
        Admin::create([
            'username' => $adminUser->username,
            'email' => $adminUser->email,
            'name' => 'Review Admin',
            'password_hash' => 'unused',
        ]);

        $this->actingAs($adminUser)
            ->postJson("/api/v1/admin/ads/{$post->id}/approve")
            ->assertOk();

        $this->assertSame('active', $post->fresh()->status->value);
        $this->getJson('/api/v1/ads')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Paid Review Product']);
    }

    public function test_subscription_listing_is_rejected_when_quota_is_empty(): void
    {
        $seller = User::factory()->create([
            'user_type' => 'seller',
            'plan_expires_at' => now()->addMonth(),
            'ads_remaining' => 0,
        ]);

        $this->actingAs($seller)->postJson('/api/v1/ads', [
            'title' => 'No Quota Product',
            'description' => 'This listing should require pay per listing.',
            'price' => 1000,
            'category' => 1,
            'condition' => 'used',
        ])->assertStatus(402)
          ->assertJsonPath('error.code', 'SUBSCRIPTION_REQUIRED');

        $this->assertDatabaseMissing('product', ['product_name' => 'No Quota Product']);
    }
}
