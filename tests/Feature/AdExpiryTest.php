<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdExpiryTest extends TestCase
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

    private function makeAd(array $overrides = []): Post
    {
        return Post::create(array_merge([
            'user_id' => User::factory()->create()->id,
            'product_name' => 'Expiry Test',
            'description' => 'Testing expiry behaviour',
            'price' => 100,
            'category' => 1,
            'condition' => 'used',
            'status' => 'active',
            'hide' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    public function test_active_scope_excludes_expired_ad(): void
    {
        $this->makeAd(['expire_date' => time() - 3600]); // already past

        $this->assertSame(0, Post::query()->active()->count());
    }

    public function test_active_scope_includes_not_yet_expired_ad(): void
    {
        $this->makeAd(['expire_date' => time() + 3600]);

        $this->assertSame(1, Post::query()->active()->count());
    }

    public function test_active_scope_includes_never_expiring_ad(): void
    {
        // Legacy rows use 0 (or NULL after the nullable migration) = never expires.
        $this->makeAd(['expire_date' => 0]);
        $this->makeAd(['expire_date' => null]);

        $this->assertSame(2, Post::query()->active()->count());
    }

    public function test_expired_ad_hidden_from_public_listing(): void
    {
        $this->makeAd(['product_name' => 'Gone Fishin', 'expire_date' => time() - 3600]);

        $this->getJson('/api/v1/ads?q=Gone')
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_expired_ad_hidden_from_public_detail(): void
    {
        $ad = $this->makeAd(['expire_date' => time() - 3600]);

        $this->getJson("/api/v1/ads/{$ad->id}-expired")
            ->assertNotFound();
    }

    public function test_expire_command_marks_due_ads_only(): void
    {
        $due = $this->makeAd(['expire_date' => time() - 3600]);
        $future = $this->makeAd(['expire_date' => time() + 3600]);
        $never = $this->makeAd(['expire_date' => 0]);
        $pending = $this->makeAd(['status' => 'pending', 'expire_date' => time() - 3600]);

        $this->artisan('ads:expire')->assertSuccessful();

        $this->assertDatabaseHas('product', ['id' => $due->id, 'status' => 'expire']);
        $this->assertDatabaseHas('product', ['id' => $future->id, 'status' => 'active']);
        $this->assertDatabaseHas('product', ['id' => $never->id, 'status' => 'active']);
        $this->assertDatabaseHas('product', ['id' => $pending->id, 'status' => 'pending']);
    }
}
