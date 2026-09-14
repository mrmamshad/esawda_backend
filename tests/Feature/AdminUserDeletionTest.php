<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('catagory_main')->insert([
            'cat_id' => 1,
            'cat_order' => 1,
            'cat_name' => 'Test Category',
            'slug' => 'test-category',
            'icon' => 'test-icon',
            'picture' => 'test.jpg',
        ]);
    }

    private function registerPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Recreated User',
            'email' => 'shop-owner@example.com',
            'phone' => '01712345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_admin_hard_delete_releases_email_and_phone_for_re_registration(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        $victim = User::factory()->create([
            'user_type' => 'seller',
            'email' => 'shop-owner@example.com',
            'phone' => '01712345678',
            'password_hash' => Hash::make('password123'),
            'shop_name' => 'Demo Shop',
            'shop_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->deleteJson("/api/v1/admin/users/{$victim->id}")
            ->assertOk();

        $this->assertDatabaseMissing('user', ['id' => $victim->id]);

        $this->postJson('/api/v1/auth/register', $this->registerPayload())
            ->assertCreated();
    }

    public function test_admin_delete_archives_when_fk_blocks_and_still_releases_identifiers(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        $seller = User::factory()->create([
            'user_type' => 'seller',
            'email' => 'shop-owner@example.com',
            'phone' => '01712345678',
            'password_hash' => Hash::make('password123'),
            'shop_name' => 'Blocked Delete Shop',
            'shop_status' => 'active',
        ]);

        $buyer = User::factory()->create(['email' => 'buyer@example.com']);

        $post = Post::create([
            'user_id' => $seller->id,
            'product_name' => 'Test Product',
            'description' => 'A sufficiently long description for validation.',
            'price' => 1500,
            'category' => 1,
            'condition' => 'new',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Order::create([
            'product_id' => $post->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'transaction_id' => null,
            'amount' => $post->price,
            'shipping_status' => 'pending',
            'seller_paid' => false,
        ]);

        $this->actingAs($admin)
            ->deleteJson("/api/v1/admin/users/{$seller->id}")
            ->assertOk()
            ->assertJsonPath('data.message', 'User archived (hard delete blocked by related records). Email/phone have been released for reuse.');

        $archived = User::findOrFail($seller->id);
        $this->assertSame('0', (string) $archived->status);
        $this->assertSame('user', (string) $archived->user_type);
        $this->assertNull($archived->phone);
        $this->assertNotSame('shop-owner@example.com', (string) $archived->email);
        $this->assertNull($archived->shop_name);

        $this->postJson('/api/v1/auth/register', $this->registerPayload())
            ->assertCreated();
    }
}
