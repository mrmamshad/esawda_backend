<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Post;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdsApiTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): array
    {
        Category::create(['cat_id' => 1, 'cat_order' => 1, 'cat_name' => 'Vehicles', 'slug' => 'vehicles', 'icon' => 'fa-car']);
        SubCategory::create(['sub_cat_id' => 10, 'main_cat_id' => 1, 'sub_cat_name' => 'Cars', 'slug' => 'cars']);

        $u = User::forceCreate([
            'username' => 'seller1', 'email' => 's1@x.com',
            'password_hash' => Hash::make('x'), 'status' => '1', 'name' => 'Seller One',
            'image' => 'default_user.png',
        ]);

        $ads = [];
        foreach (range(1, 5) as $i) {
            $ads[] = Post::create([
                'status' => 'active',
                'hide' => '0',
                'user_id' => $u->id,
                'featured' => $i === 1 ? '1' : '0',
                'product_name' => "BMW 52{$i}i",
                'slug' => "bmw-52{$i}i",
                'description' => 'Well maintained sedan.',
                'category' => 1,
                'sub_category' => 10,
                'price' => 10000 + $i * 1000,
                'city' => $i % 2 ? 'Dhaka' : 'Chittagong',
                'country' => 'BD',
                'view' => 0,
                'created_at' => now()->subDays($i),
                'updated_at' => now()->subDays($i),
            ]);
        }

        return ['user' => $u, 'ads' => $ads];
    }

    public function test_index_returns_wrapped_paginated_list(): void
    {
        $this->seedData();
        $r = $this->getJson('/api/v1/ads?per_page=3')->assertOk();
        $r->assertJsonStructure([
            'data' => [['id', 'url_slug', 'title', 'price', 'thumbnail', 'featured', 'location', 'category']],
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            'links' => ['first', 'last', 'prev', 'next'],
        ])->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.per_page', 3)
            ->assertJsonCount(3, 'data');
    }

    public function test_index_orders_featured_first_by_default(): void
    {
        $this->seedData();
        $r = $this->getJson('/api/v1/ads')->assertOk();
        $this->assertTrue($r->json('data.0.featured'), 'Featured ad should appear first');
    }

    public function test_filter_grammar_price_range_and_city(): void
    {
        $this->seedData();
        $r = $this->getJson('/api/v1/ads?filter[price][gte]=13000&filter[price][lte]=14000')->assertOk();
        $this->assertSame(2, $r->json('meta.total'));

        $r = $this->getJson('/api/v1/ads?filter[city]=Dhaka')->assertOk();
        foreach ($r->json('data') as $d) {
            $this->assertSame('Dhaka', $d['location']['city']);
        }
    }

    public function test_free_text_search(): void
    {
        $this->seedData();
        $r = $this->getJson('/api/v1/ads?q=523')->assertOk();
        $this->assertSame(1, $r->json('meta.total'));
        $this->assertStringContainsString('523', $r->json('data.0.title'));
    }

    public function test_show_by_id_slug_and_id_only(): void
    {
        ['ads' => $ads] = $this->seedData();
        $ad = $ads[0];

        $this->getJson("/api/v1/ads/{$ad->id}-{$ad->slug}")
            ->assertOk()
            ->assertJsonPath('data.id', $ad->id)
            ->assertJsonStructure(['data' => ['id', 'title', 'description', 'images', 'seller', 'category']]);

        $this->getJson("/api/v1/ads/{$ad->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $ad->id);
    }

    public function test_show_increments_view_counter(): void
    {
        ['ads' => $ads] = $this->seedData();
        $this->getJson("/api/v1/ads/{$ads[0]->id}")->assertOk();
        $this->assertSame(1, Post::find($ads[0]->id)->view);
    }

    public function test_featured_endpoint(): void
    {
        $this->seedData();
        $r = $this->getJson('/api/v1/ads/featured?limit=3')->assertOk();
        $this->assertCount(1, $r->json('data'));
        $this->assertTrue($r->json('data.0.featured'));
    }

    public function test_similar_excludes_self(): void
    {
        ['ads' => $ads] = $this->seedData();
        $r = $this->getJson("/api/v1/ads/{$ads[0]->id}/similar")->assertOk();
        foreach ($r->json('data') as $d) {
            $this->assertNotSame($ads[0]->id, $d['id']);
        }
        $this->assertGreaterThan(0, count($r->json('data')));
    }

    public function test_search_suggest_returns_slim_hits(): void
    {
        $this->seedData();
        $r = $this->getJson('/api/v1/ads/search-suggest?q=BMW')->assertOk();
        $this->assertJsonEquivalent = null; // silence phpstan
        $this->assertGreaterThan(0, count($r->json('data')));
        $this->assertArrayHasKey('url_slug', $r->json('data.0'));
    }

    public function test_search_suggest_needs_two_chars(): void
    {
        $this->seedData();
        $r = $this->getJson('/api/v1/ads/search-suggest?q=B')->assertOk();
        $this->assertSame([], $r->json('data'));
    }
}
