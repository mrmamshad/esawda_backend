<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\AdMutationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdImageLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_new_post_allows_images_to_be_omitted(): void
    {
        $user = User::factory()->create([
            'plan_id' => 1,
            'plan_expires_at' => now()->addMonth(),
            'ads_remaining' => 2,
        ]);
        $category = Category::create(['cat_name' => 'Electronics', 'slug' => 'electronics']);

        $this->actingAs($user)
            ->postJson('/api/v1/ads', $this->validPayload($category->cat_id))
            ->assertCreated();

        $this->assertDatabaseHas('product', ['product_name' => 'Test product']);
    }

    public function test_new_post_rejects_more_than_four_images(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['cat_name' => 'Electronics', 'slug' => 'electronics']);
        $payload = $this->validPayload($category->cat_id);
        $payload['images'] = array_map(
            fn (int $number) => UploadedFile::fake()->image("product-{$number}.jpg"),
            range(1, 5),
        );

        $this->actingAs($user)
            ->post('/api/v1/ads', $payload, ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonStructure(['error' => ['fields' => ['images']]]);
    }

    public function test_service_allows_four_total_images_but_rejects_a_fifth(): void
    {
        $post = $this->postWithImages(['one.jpg', 'two.jpg', 'three.jpg']);

        app(AdMutationService::class)->update(
            $post,
            [],
            [UploadedFile::fake()->image('four.jpg')],
        );
        $this->assertCount(4, $this->images($post->fresh()));

        try {
            app(AdMutationService::class)->update(
                $post->fresh(),
                [],
                [UploadedFile::fake()->image('five.jpg')],
            );
            $this->fail('A fifth product image should have been rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('images', $e->errors());
        }

        $this->assertCount(4, $this->images($post->fresh()));
    }

    private function validPayload(int $categoryId): array
    {
        return [
            'title' => 'Test product',
            'description' => 'A valid product description.',
            'condition' => 'used',
            'category' => $categoryId,
            'price' => 1000,
        ];
    }

    private function postWithImages(array $images): Post
    {
        return Post::create([
            'status' => 'active',
            'hide' => '0',
            'user_id' => User::factory()->create()->id,
            'product_name' => 'Existing product',
            'slug' => 'existing-product',
            'price' => 1000,
            'condition' => 'used',
            'expire_date' => 0,
            'screen_shot' => json_encode($images),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function images(Post $post): array
    {
        return is_array($post->screen_shot)
            ? $post->screen_shot
            : (json_decode((string) $post->screen_shot, true) ?: []);
    }
}
