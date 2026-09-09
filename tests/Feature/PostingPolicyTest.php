<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Post;
use App\Models\User;
use App\Services\PostingPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostingPolicyTest extends TestCase
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

    private function adData(): array
    {
        return [
            'title' => 'Policy Test Ad',
            'description' => 'A sufficiently long description for validation.',
            'price' => 1000,
            'category' => 1,
            'condition' => 'used',
        ];
    }

    public function test_single_user_posts_free_by_default(): void
    {
        $user = User::factory()->create(['user_type' => 'user']);

        $this->assertTrue(PostingPolicy::canPostFree($user));

        $this->actingAs($user)
            ->postJson('/api/v1/ads', $this->adData())
            ->assertCreated();

        // Globally-free posts never burn quota.
        $this->assertSame(0, (int) $user->fresh()->ads_remaining);
    }

    public function test_shop_needs_subscription_by_default(): void
    {
        $shop = User::factory()->create(['user_type' => 'seller']);

        $this->assertFalse(PostingPolicy::canPostFree($shop));

        $this->actingAs($shop)
            ->postJson('/api/v1/ads', $this->adData())
            ->assertStatus(402);
    }

    public function test_global_switch_turns_shop_subscription_off(): void
    {
        Option::updateOrCreate(['option_name' => 'shop_subscription_required'], ['option_value' => '0']);
        $shop = User::factory()->create(['user_type' => 'seller']);

        $this->assertTrue(PostingPolicy::canPostFree($shop));

        $this->actingAs($shop)
            ->postJson('/api/v1/ads', $this->adData())
            ->assertCreated();
    }

    public function test_free_policy_bypasses_subscription_and_blocked_cannot_post(): void
    {
        $freeShop = User::factory()->create(['user_type' => 'seller', 'post_policy' => 'free']);
        $this->assertTrue(PostingPolicy::canPostFree($freeShop));
        $this->actingAs($freeShop)
            ->postJson('/api/v1/ads', $this->adData())
            ->assertCreated();

        $blocked = User::factory()->create(['user_type' => 'user', 'post_policy' => 'blocked']);
        $this->assertTrue(PostingPolicy::isBlocked($blocked));
        $this->actingAs($blocked)
            ->postJson('/api/v1/ads', $this->adData())
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'POSTING_BLOCKED');
    }

    public function test_blocked_shop_cannot_bypass_via_paid_listing(): void
    {
        $blocked = User::factory()->create(['user_type' => 'seller', 'post_policy' => 'blocked']);

        $this->actingAs($blocked)
            ->postJson('/api/v1/checkout/paid-listing', array_merge($this->adData(), [
                'policies_accepted' => true,
            ]))
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'POSTING_BLOCKED');
    }

    public function test_subscription_quota_still_burns_when_it_entitles_the_post(): void
    {
        Option::updateOrCreate(['option_name' => 'shop_subscription_required'], ['option_value' => '1']);
        $shop = User::factory()->create([
            'user_type' => 'seller',
            'plan_expires_at' => now()->addMonth(),
            'ads_remaining' => 3,
        ]);

        $this->actingAs($shop)
            ->postJson('/api/v1/ads', $this->adData())
            ->assertCreated();

        $this->assertSame(2, (int) $shop->fresh()->ads_remaining);
    }

    public function test_admin_can_set_post_policy_per_account(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $shop = User::factory()->create(['user_type' => 'seller']);

        $this->actingAs($admin)
            ->patchJson("/api/v1/admin/users/{$shop->id}", ['post_policy' => 'free'])
            ->assertOk();

        $this->assertSame('free', $shop->fresh()->post_policy);
        $this->assertTrue(PostingPolicy::canPostFree($shop->fresh()));
    }

    public function test_user_index_reports_listing_counts(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $user = User::factory()->create(['user_type' => 'user']);
        Post::create([
            'user_id' => $user->id,
            'product_name' => 'Counted Ad',
            'description' => 'A sufficiently long description.',
            'price' => 50,
            'category' => 1,
            'condition' => 'new',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/users?per_page=100')
            ->assertOk()
            ->assertJsonFragment(['id' => $user->id, 'listings_total' => 1, 'listings_active' => 1]);
    }
}
