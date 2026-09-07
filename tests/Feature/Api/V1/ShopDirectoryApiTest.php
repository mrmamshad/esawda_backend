<?php

namespace Tests\Feature\Api\V1;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopDirectoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_returns_only_active_shop_profiles(): void
    {
        $verified = $this->createShop('Verified Electronics', 'Electronics', verified: true);
        $this->createShop('Fashion House', 'Fashion & Apparel');
        $this->createShop('Suspended Shop', 'Electronics', status: '0');
        User::factory()->create(['user_type' => 'user', 'shop_name' => null]);

        $this->createProduct($verified, 'Active Phone');
        $this->createProduct($verified, 'Expired Phone', 'expire');

        $response = $this->getJson('/api/v1/shops?per_page=12')->assertOk();

        $response->assertJsonStructure([
            'data' => [[
                'id', 'username', 'name', 'shop_name', 'shop_category',
                'shop_category_slug', 'shop_description', 'shop_verified',
                'avatar_url', 'cover_url', 'shop_banner_url', 'online',
                'location' => ['address', 'city', 'country'],
                'stats' => ['active_products', 'total_products'],
                'member_since',
            ]],
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            'links' => ['first', 'last', 'prev', 'next'],
        ])->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.shop_name', 'Verified Electronics')
            ->assertJsonPath('data.0.shop_verified', true)
            ->assertJsonPath('data.0.stats.active_products', 1)
            ->assertJsonPath('data.0.stats.total_products', 2);
    }

    public function test_shop_categories_are_separate_and_include_live_counts(): void
    {
        $this->createShop('Phone World', 'Electronics');
        $this->createShop('Laptop World', 'Electronics');
        $this->createShop('Style House', 'Fashion & Apparel');
        $this->createShop('Uncategorised Shop', null);
        $this->createShop('Hidden Style', 'Fashion & Apparel', status: '0');

        $response = $this->getJson('/api/v1/shop-categories')->assertOk();

        $response->assertJsonPath('data.0.name', 'Electronics')
            ->assertJsonPath('data.0.slug', 'electronics')
            ->assertJsonPath('data.0.shops_count', 2)
            ->assertJsonPath('data.1.name', 'Fashion & Apparel')
            ->assertJsonPath('data.1.shops_count', 1)
            ->assertJsonPath('meta.total_shops', 4);

        $this->assertCount(12, $response->json('data'));
    }

    public function test_directory_filters_by_category_slug_and_searches_shop_profiles(): void
    {
        $this->createShop('Phone World', 'Electronics', description: 'Smartphones and accessories');
        $this->createShop('Dhaka Style House', 'Fashion & Apparel', description: 'Local clothing');
        $this->createShop('Chattogram Fashion', 'Fashion & Apparel', description: 'Designer wear');

        $filtered = $this->getJson('/api/v1/shops?filter[category]=fashion-apparel')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);

        foreach ($filtered->json('data') as $shop) {
            $this->assertSame('Fashion & Apparel', $shop['shop_category']);
        }

        $this->getJson('/api/v1/shops?filter[category]=FASHION%20%26%20APPAREL')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);

        $this->getJson('/api/v1/shops?q=accessories')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.shop_name', 'Phone World');

        $this->getJson('/api/v1/shops?filter[category]=not-a-category')
            ->assertOk()
            ->assertJsonPath('meta.total', 0);
    }

    public function test_directory_can_sort_by_active_product_count(): void
    {
        $small = $this->createShop('Small Shop', 'Other');
        $large = $this->createShop('Large Shop', 'Other');
        $this->createProduct($small, 'One product');
        $this->createProduct($large, 'First product');
        $this->createProduct($large, 'Second product');

        $this->getJson('/api/v1/shops?sort=-active_products')
            ->assertOk()
            ->assertJsonPath('data.0.shop_name', 'Large Shop')
            ->assertJsonPath('data.0.stats.active_products', 2);
    }

    private function createShop(
        string $shopName,
        ?string $category,
        string $status = '1',
        bool $verified = false,
        ?string $description = null,
    ): User {
        return User::factory()->create([
            'user_type' => 'seller',
            'status' => $status,
            'shop_name' => $shopName,
            'shop_category' => $category,
            'shop_description' => $description,
            'shop_address' => 'Dhaka, Bangladesh',
            'shop_verified_at' => $verified ? now() : null,
        ]);
    }

    private function createProduct(User $shop, string $name, string $status = 'active'): Post
    {
        return Post::create([
            'status' => $status,
            'hide' => '0',
            'user_id' => $shop->id,
            'product_name' => $name,
            'slug' => str($name)->slug(),
            'price' => 1000,
            'condition' => 'new',
            'expire_date' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
