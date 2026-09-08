<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCategoryApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->admin = User::factory()->create(['user_type' => 'admin']);
    }

    public function test_admin_can_create_category_with_an_image(): void
    {
        $response = $this->actingAs($this->admin)->post('/api/v1/admin/categories', [
            'cat_name' => 'Electronics',
            'slug' => 'electronics',
            'icon' => 'fa-mobile',
            'cat_order' => 3,
            'picture' => UploadedFile::fake()->image('electronics.jpg', 1200, 800),
        ], ['Accept' => 'application/json'])->assertCreated();

        $picture = (string) $response->json('data.picture');
        $this->assertStringStartsWith('categories/', $picture);
        $response->assertJsonPath('data.cat_name', 'Electronics')
            ->assertJsonPath('data.icon', 'fa-mobile')
            ->assertJsonPath('data.cat_order', 3);
        $this->assertStringEndsWith('/storage/site/'.$picture, $response->json('data.picture_url'));
        Storage::disk('public')->assertExists('site/'.$picture);
    }

    public function test_replacing_and_removing_picture_cleans_up_managed_files(): void
    {
        $created = $this->actingAs($this->admin)->post('/api/v1/admin/categories', [
            'cat_name' => 'Fashion',
            'picture' => UploadedFile::fake()->image('old.jpg'),
        ], ['Accept' => 'application/json'])->assertCreated();
        $id = (int) $created->json('data.cat_id');
        $oldPicture = (string) $created->json('data.picture');

        $updated = $this->actingAs($this->admin)->post("/api/v1/admin/categories/{$id}", [
            '_method' => 'PATCH',
            'cat_name' => 'Fashion & Apparel',
            'picture' => UploadedFile::fake()->image('new.webp'),
        ], ['Accept' => 'application/json'])->assertOk();
        $newPicture = (string) $updated->json('data.picture');

        $this->assertNotSame($oldPicture, $newPicture);
        Storage::disk('public')->assertMissing('site/'.$oldPicture);
        Storage::disk('public')->assertExists('site/'.$newPicture);

        $this->actingAs($this->admin)
            ->patchJson("/api/v1/admin/categories/{$id}", ['remove_picture' => true])
            ->assertOk()
            ->assertJsonPath('data.picture', null)
            ->assertJsonPath('data.picture_url', null);
        Storage::disk('public')->assertMissing('site/'.$newPicture);
    }

    public function test_admin_can_update_all_category_fields(): void
    {
        $category = Category::create(['cat_name' => 'Old', 'slug' => 'old', 'icon' => 'fa-tag', 'cat_order' => 1]);

        $this->actingAs($this->admin)->patchJson("/api/v1/admin/categories/{$category->cat_id}", [
            'cat_name' => 'Home & Living',
            'slug' => 'home-living',
            'icon' => 'fa-home',
            'cat_order' => 8,
        ])->assertOk()
            ->assertJsonPath('data.cat_name', 'Home & Living')
            ->assertJsonPath('data.slug', 'home-living')
            ->assertJsonPath('data.icon', 'fa-home')
            ->assertJsonPath('data.cat_order', 8);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $category = Category::create(['cat_name' => 'Vehicles', 'slug' => 'vehicles']);
        SubCategory::create([
            'main_cat_id' => $category->cat_id,
            'sub_cat_name' => 'Cars',
            'slug' => 'cars',
            'cat_order' => 1,
        ]);

        $this->actingAs($this->admin)
            ->deleteJson("/api/v1/admin/categories/{$category->cat_id}")
            ->assertConflict()
            ->assertJsonPath('error.code', 'CATEGORY_NOT_EMPTY');
        $this->assertDatabaseHas('catagory_main', ['cat_id' => $category->cat_id]);
    }

    public function test_empty_category_deletion_removes_its_managed_picture(): void
    {
        Storage::disk('public')->put('site/categories/delete-me.jpg', 'image');
        $category = Category::create([
            'cat_name' => 'Temporary',
            'slug' => 'temporary',
            'picture' => 'categories/delete-me.jpg',
        ]);

        $this->actingAs($this->admin)
            ->deleteJson("/api/v1/admin/categories/{$category->cat_id}")
            ->assertOk();

        Storage::disk('public')->assertMissing('site/categories/delete-me.jpg');
        $this->assertDatabaseMissing('catagory_main', ['cat_id' => $category->cat_id]);
    }

    public function test_non_admin_cannot_manage_categories(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/api/v1/admin/categories', ['cat_name' => 'Forbidden'])
            ->assertForbidden();
    }
}
