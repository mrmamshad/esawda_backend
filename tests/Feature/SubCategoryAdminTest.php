<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubCategoryAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['user_type' => 'admin']);
    }

    private function category(): Category
    {
        return Category::create([
            'cat_name' => 'Vehicles',
            'slug' => 'vehicles',
            'cat_order' => 1,
        ]);
    }

    public function test_store_creates_subcategory_with_generated_slug(): void
    {
        $cat = $this->category();

        $this->actingAs($this->admin())
            ->postJson('/api/v1/admin/subcategories', [
                'main_cat_id' => $cat->cat_id,
                'sub_cat_name' => 'Motor Bikes',
            ])
            ->assertCreated()
            ->assertJsonPath('data.sub_cat_name', 'Motor Bikes')
            ->assertJsonPath('data.slug', 'motor-bikes');

        $this->assertDatabaseHas('catagory_sub', [
            'main_cat_id' => $cat->cat_id,
            'sub_cat_name' => 'Motor Bikes',
        ]);
    }

    public function test_store_rejects_unknown_parent_category(): void
    {
        $this->actingAs($this->admin())
            ->postJson('/api/v1/admin/subcategories', [
                'main_cat_id' => 999999,
                'sub_cat_name' => 'Ghost',
            ])
            ->assertStatus(422);
    }

    public function test_update_renames_and_reorders(): void
    {
        $sub = SubCategory::create([
            'main_cat_id' => $this->category()->cat_id,
            'sub_cat_name' => 'Old Name',
            'slug' => 'old-name',
            'cat_order' => 3,
        ]);

        $this->actingAs($this->admin())
            ->putJson("/api/v1/admin/subcategories/{$sub->sub_cat_id}", [
                'sub_cat_name' => 'New Name',
                'cat_order' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('data.sub_cat_name', 'New Name')
            ->assertJsonPath('data.cat_order', 1);
    }

    public function test_destroy_is_blocked_while_products_exist(): void
    {
        $cat = $this->category();
        $sub = SubCategory::create([
            'main_cat_id' => $cat->cat_id,
            'sub_cat_name' => 'Used Cars',
            'slug' => 'used-cars',
        ]);
        $user = User::factory()->create();
        Post::create([
            'user_id' => $user->id,
            'product_name' => 'Blocked Ad',
            'description' => 'Cannot delete parent',
            'price' => 100,
            'category' => $cat->cat_id,
            'sub_category' => $sub->sub_cat_id,
            'condition' => 'used',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin())
            ->deleteJson("/api/v1/admin/subcategories/{$sub->sub_cat_id}")
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SUBCATEGORY_IN_USE');

        $this->assertDatabaseHas('catagory_sub', ['sub_cat_id' => $sub->sub_cat_id]);
    }

    public function test_destroy_removes_empty_subcategory(): void
    {
        $sub = SubCategory::create([
            'main_cat_id' => $this->category()->cat_id,
            'sub_cat_name' => 'Empty',
            'slug' => 'empty',
        ]);

        $this->actingAs($this->admin())
            ->deleteJson("/api/v1/admin/subcategories/{$sub->sub_cat_id}")
            ->assertOk();

        $this->assertDatabaseMissing('catagory_sub', ['sub_cat_id' => $sub->sub_cat_id]);
    }

    public function test_non_admins_are_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['user_type' => 'seller']))
            ->getJson('/api/v1/admin/subcategories')
            ->assertForbidden();
    }
}
