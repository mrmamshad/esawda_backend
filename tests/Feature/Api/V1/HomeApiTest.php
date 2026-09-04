<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Post;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_returns_aggregate_payload(): void
    {
        Category::create(['cat_id' => 1, 'cat_order' => 1, 'cat_name' => 'Vehicles', 'slug' => 'vehicles', 'icon' => 'fa-car']);
        SubCategory::create(['sub_cat_id' => 10, 'main_cat_id' => 1, 'sub_cat_name' => 'Cars', 'slug' => 'cars']);

        $u = User::forceCreate([
            'username' => 'seller1', 'email' => 's1@x.com',
            'password_hash' => Hash::make('x'), 'status' => '1', 'name' => 'Seller One',
            'image' => 'default_user.png',
        ]);

        Post::create([
            'status' => 'active', 'hide' => '0', 'user_id' => $u->id,
            'featured' => '1', 'condition' => 'used',
            'product_name' => 'Test Sedan', 'slug' => 'test-sedan',
            'description' => 'A fine car.', 'category' => 1, 'sub_category' => 10,
            'price' => 5000, 'city' => 'Dhaka', 'country' => 'BD', 'view' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $r = $this->getJson('/api/v1/home')->assertOk();

        $r->assertJsonStructure([
            'data' => [
                'settings',
                'categories',
                'sections' => ['featured', 'urgent', 'last24h', 'highlights', 'used'],
                'plans', 'testimonials', 'blogs',
            ],
        ]);

        // One featured+used ad lands in both the featured.used and used.used rails.
        $this->assertCount(1, $r->json('data.sections.featured.used'));
        $this->assertCount(1, $r->json('data.sections.used.used'));
        $this->assertSame('Test Sedan', $r->json('data.sections.featured.used.0.title'));

        // Second hit comes from cache and matches.
        $again = $this->getJson('/api/v1/home')->assertOk();
        $this->assertSame($r->json('data'), $again->json('data'));
    }
}
