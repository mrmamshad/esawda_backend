<?php

namespace Tests\Feature\Api\V1;

use App\Models\Order;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CodOrderTest extends TestCase
{
    use RefreshDatabase;

    private function shop(): User
    {
        return User::factory()->create([
            'user_type' => 'seller',
            'shop_name' => 'Test Shop',
        ]);
    }

    private function product(User $seller, array $overrides = []): Post
    {
        return Post::create(array_merge([
            'status' => 'active',
            'hide' => '0',
            'user_id' => $seller->id,
            'product_name' => 'iPhone 13',
            'slug' => 'iphone-13',
            'description' => 'Good condition.',
            'category' => 2,
            'price' => 60000,
            'view' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    public function test_guest_can_place_a_cod_order(): void
    {
        Cache::flush();
        $seller = $this->shop();
        $product = $this->product($seller);

        $res = $this->postJson("/api/v1/ads/{$product->id}/order", [
            'name' => 'Rahim Uddin',
            'phone' => '01712345678',
            'address' => 'House 12, Road 5, Dhanmondi, Dhaka',
        ]);

        $res->assertCreated()->assertJsonPath('data.status', 'pending');

        $order = Order::firstWhere('product_id', $product->id);
        $this->assertNotNull($order);
        $this->assertNull($order->buyer_id);
        $this->assertSame('Rahim Uddin', $order->buyer_name);
        $this->assertSame('01712345678', $order->buyer_phone);
        $this->assertSame($seller->id, $order->seller_id);
        $this->assertSame(60000.0, (float) $order->amount);
    }

    public function test_order_requires_a_valid_bd_phone(): void
    {
        Cache::flush();
        $seller = $this->shop();
        $product = $this->product($seller);

        $this->postJson("/api/v1/ads/{$product->id}/order", [
            'name' => 'Rahim',
            'phone' => '12345',
            'address' => 'Some long enough address here',
        ])->assertUnprocessable()->assertJsonPath('error.fields.phone', [
            'The phone field format is invalid.',
        ]);
    }

    public function test_order_rejected_for_non_shop_seller(): void
    {
        Cache::flush();
        $user = User::factory()->create(['user_type' => 'user']);
        $product = $this->product($user);

        $this->postJson("/api/v1/ads/{$product->id}/order", [
            'name' => 'Rahim',
            'phone' => '01712345678',
            'address' => 'A sufficiently long delivery address',
        ])->assertStatus(422)->assertJsonPath('error.code', 'NO_ORDERS');
    }

    public function test_order_rejected_for_free_product(): void
    {
        Cache::flush();
        $seller = $this->shop();
        $product = $this->product($seller, ['price' => 0]);

        $this->postJson("/api/v1/ads/{$product->id}/order", [
            'name' => 'Rahim',
            'phone' => '01712345678',
            'address' => 'A sufficiently long delivery address',
        ])->assertStatus(422)->assertJsonPath('error.code', 'NOT_FOR_SALE');
    }

    public function test_seller_updates_own_order_status(): void
    {
        Cache::flush();
        $seller = $this->shop();
        $product = $this->product($seller);

        $this->postJson("/api/v1/ads/{$product->id}/order", [
            'name' => 'Rahim',
            'phone' => '01712345678',
            'address' => 'A sufficiently long delivery address',
        ])->assertCreated();

        $order = Order::firstWhere('product_id', $product->id);
        Sanctum::actingAs($seller);

        foreach (['confirmed', 'delivered'] as $status) {
            $this->patchJson("/api/v1/me/orders/{$order->id}/status", ['shipping_status' => $status])
                ->assertOk()
                ->assertJsonPath('data.status', $status);
        }

        $this->assertSame('delivered', $order->fresh()->shipping_status);
    }

    public function test_seller_cannot_update_another_shops_order(): void
    {
        Cache::flush();
        $seller = $this->shop();
        $other = $this->shop();
        $product = $this->product($seller);

        $this->postJson("/api/v1/ads/{$product->id}/order", [
            'name' => 'Rahim',
            'phone' => '01712345678',
            'address' => 'A sufficiently long delivery address',
        ])->assertCreated();

        $order = Order::firstWhere('product_id', $product->id);
        Sanctum::actingAs($other);

        $this->patchJson("/api/v1/me/orders/{$order->id}/status", ['shipping_status' => 'delivered'])
            ->assertNotFound();
        $this->assertSame('pending', $order->fresh()->shipping_status);
    }

    public function test_invalid_status_value_is_rejected(): void
    {
        Cache::flush();
        $seller = $this->shop();
        $product = $this->product($seller);
        $order = Order::create([
            'product_id' => $product->id,
            'seller_id' => $seller->id,
            'amount' => 60000,
            'shipping_status' => 'pending',
        ]);
        Sanctum::actingAs($seller);

        $this->patchJson("/api/v1/me/orders/{$order->id}/status", ['shipping_status' => 'refunded'])
            ->assertUnprocessable();
    }

    public function test_admin_summary_groups_orders_per_shop(): void
    {
        Cache::flush();
        $a = $this->shop();
        $b = $this->shop();
        $pa = $this->product($a, ['slug' => 'a-1']);
        $pb = $this->product($b, ['slug' => 'b-1', 'price' => 1000]);

        Order::create(['product_id' => $pa->id, 'seller_id' => $a->id, 'amount' => 60000, 'shipping_status' => 'pending']);
        Order::create(['product_id' => $pb->id, 'seller_id' => $b->id, 'amount' => 1000, 'shipping_status' => 'delivered']);
        Order::create(['product_id' => $pb->id, 'seller_id' => $b->id, 'amount' => 1000, 'shipping_status' => 'cancelled']);

        Sanctum::actingAs(User::factory()->create(['user_type' => 'admin']));

        $res = $this->getJson('/api/v1/admin/orders/summary')->assertOk();
        $rows = collect($res->json('data'))->keyBy('seller_id');

        $this->assertSame(1, $rows[$a->id]['orders']);
        $this->assertSame(60000.0, (float) $rows[$a->id]['total_amount']);
        $this->assertSame(2, $rows[$b->id]['orders']);
        $this->assertSame(2000.0, (float) $rows[$b->id]['total_amount']);
        $this->assertSame(1, $rows[$b->id]['delivered_count']);
        $this->assertSame(1, $rows[$b->id]['cancelled_count']);
    }
}
