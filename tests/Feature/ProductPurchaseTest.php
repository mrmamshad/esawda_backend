<?php

namespace Tests\Feature;

use App\Jobs\FulfilTransactionJob;
use App\Models\Order;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('catagory_main')->insert([
            'cat_id' => 1,
            'cat_order' => 1,
            'cat_name' => 'Test Category',
            'slug' => 'test-category',
            'icon' => 'test-icon',
            'picture' => 'test.jpg',
        ]);
    }

    private function activePost(int $sellerId, array $overrides = []): Post
    {
        return Post::create(array_merge([
            'user_id' => $sellerId,
            'product_name' => 'Buyable Product',
            'description' => 'A product for the buy-now flow',
            'price' => 1500,
            'category' => 1,
            'condition' => 'new',
            'status' => 'active',
            'hide' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    public function test_product_purchase_creates_order_and_transaction(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $post = $this->activePost($seller->id);

        $response = $this->actingAs($buyer)
            ->postJson("/api/v1/checkout/product-purchase/{$post->id}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['transaction_id', 'order_id', 'gateway_url']]);

        $tx = Transaction::where('purpose', 'product_purchase')->firstOrFail();
        $this->assertSame('pending', $tx->status->value);

        $this->assertDatabaseHas('orders', [
            'product_id' => $post->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'transaction_id' => $tx->id,
            'amount' => 1500,
            'shipping_status' => 'pending',
            'seller_paid' => false,
        ]);
    }

    public function test_owner_cannot_buy_own_product(): void
    {
        $seller = User::factory()->create();
        $post = $this->activePost($seller->id);

        $this->actingAs($seller)
            ->postJson("/api/v1/checkout/product-purchase/{$post->id}")
            ->assertStatus(422)
            ->assertJson(['error' => ['code' => 'OWN_PRODUCT']]);
    }

    public function test_fulfil_marks_order_processing_and_post_sold_out(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $post = $this->activePost($seller->id);

        $tx = Transaction::create([
            'seller_id' => $buyer->id,
            'product_id' => $post->id,
            'amount' => 1500,
            'status' => 'success',
            'purpose' => 'product_purchase',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode(['buyer_id' => $buyer->id, 'seller_id' => $seller->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order = Order::create([
            'product_id' => $post->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'transaction_id' => $tx->id,
            'amount' => 1500,
            'shipping_status' => 'pending',
            'seller_paid' => false,
        ]);

        FulfilTransactionJob::dispatchSync($tx->id);

        $this->assertSame('processing', $order->fresh()->shipping_status);
        $this->assertDatabaseHas('product', [
            'id' => $post->id,
            'status' => 'sold_out',
            'transaction_id' => $tx->id,
        ]);
    }

    public function test_fulfil_creates_order_when_missing(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $post = $this->activePost($seller->id);

        $tx = Transaction::create([
            'seller_id' => $buyer->id,
            'product_id' => $post->id,
            'amount' => 1500,
            'status' => 'success',
            'purpose' => 'product_purchase',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode(['buyer_id' => $buyer->id, 'seller_id' => $seller->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatchSync($tx->id);

        $this->assertDatabaseHas('orders', [
            'product_id' => $post->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'transaction_id' => $tx->id,
            'shipping_status' => 'processing',
        ]);
    }

    public function test_sold_out_post_is_hidden_from_public_detail(): void
    {
        $seller = User::factory()->create();
        $post = $this->activePost($seller->id, ['status' => 'sold_out']);

        $this->getJson("/api/v1/ads/{$post->id}-buyable-product")->assertNotFound();
    }

    public function test_admin_can_update_order_shipping(): void
    {
        $admin = User::factory()->admin()->create();
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $post = $this->activePost($seller->id);

        $order = Order::create([
            'product_id' => $post->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'transaction_id' => null,
            'amount' => 1500,
            'shipping_status' => 'processing',
            'seller_paid' => false,
        ]);

        $this->actingAs($admin)
            ->patchJson("/api/v1/admin/orders/{$order->id}", [
                'shipping_status' => 'shipped',
                'courier_name' => 'Steadfast',
                'tracking_no' => 'ST123456',
                'seller_paid' => true,
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'shipping_status' => 'shipped',
                    'courier_name' => 'Steadfast',
                    'tracking_no' => 'ST123456',
                    'seller_paid' => true,
                ],
            ]);
    }

    public function test_non_admin_cannot_access_admin_orders(): void
    {
        $seller = User::factory()->create();

        $this->actingAs($seller)
            ->getJson('/api/v1/admin/orders')
            ->assertStatus(403);
    }
}
