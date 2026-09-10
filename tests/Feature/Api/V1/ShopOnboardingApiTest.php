<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShopOnboardingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_application_requires_authentication(): void
    {
        $this->postJson('/api/v1/me/shop/apply')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_shop_application_validates_required_fields(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/me/shop/apply', [])
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonStructure([
                'error' => [
                    'fields' => [
                        'owner_name',
                        'owner_phone',
                        'shop_name',
                        'shop_address',
                    ],
                ],
            ]);
    }

    public function test_shop_application_succeeds_without_documents(): void
    {
        Storage::fake('public');
        Mail::fake();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->post('/api/v1/me/shop/apply', [
            'owner_name' => 'Karim Sheikh',
            'owner_phone' => '01800000000',
            'shop_name' => 'Karim Store',
            'shop_address' => 'Mirpur, Dhaka',
        ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('data.user.is_shop', true);

        $this->assertSame([], User::firstWhere('shop_name', 'Karim Store')->shop_documents ?? []);
    }

    public function test_shop_application_rejects_a_category_outside_the_configured_list(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->create());

        $this->post('/api/v1/me/shop/apply', [
            'owner_name' => 'Rahim Uddin',
            'owner_phone' => '01700000000',
            'shop_name' => 'Rahim Shop',
            'shop_address' => 'Dhaka',
            'shop_category' => 'Product-only category',
            'documents' => [
                'nid' => UploadedFile::fake()->create('nid.pdf', 100, 'application/pdf'),
                'trade_licence' => UploadedFile::fake()->create('trade.pdf', 100, 'application/pdf'),
            ],
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonStructure(['error' => ['fields' => ['shop_category']]]);
    }

    public function test_user_can_open_a_shop_and_receive_seller_access(): void
    {
        // Fake mail: the endpoint queues welcome/admin emails on the sync
        // driver in tests, and serializing the Sanctum mock token attached
        // by actingAs() blows up (Mockery __sleep). Mail delivery itself is
        // covered by TransactionalEmailsTest.
        Mail::fake();
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Old Name',
            'phone' => null,
        ]);
        // Simulate a complimentary guest quota granted at signup — it must be
        // wiped once the account becomes a seller (shops have no free trial).
        $user->forceFill([
            'ads_remaining' => 5,
            'plan_expires_at' => now()->addDays(30),
        ])->save();
        Sanctum::actingAs($user);

        $response = $this->post('/api/v1/me/shop/apply', [
            'owner_name' => 'Rahim Uddin',
            'owner_phone' => '01700000000',
            'shop_name' => 'Rahim Electronics',
            'shop_address' => 'Dhanmondi, Dhaka',
            'shop_category' => 'Electronics',
            'shop_description' => 'Phones, laptops and accessories.',
            'documents' => [
                'nid' => UploadedFile::fake()->create('nid.pdf', 200, 'application/pdf'),
                'trade_licence' => UploadedFile::fake()->create('trade-licence.pdf', 200, 'application/pdf'),
            ],
        ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('data.message', 'Your shop is open.')
            ->assertJsonPath('data.user.is_shop', true)
            ->assertJsonPath('data.user.user_type', 'seller')
            ->assertJsonPath('data.user.shop_name', 'Rahim Electronics');

        $user->refresh();

        $this->assertSame('seller', $user->user_type);
        $this->assertSame('Rahim Uddin', $user->name);
        $this->assertSame('01700000000', $user->phone);
        $this->assertSame('Rahim Electronics', $user->shop_name);
        $this->assertSame('Dhanmondi, Dhaka', $user->shop_address);
        $this->assertIsArray($user->shop_documents);
        $this->assertArrayHasKey('nid', $user->shop_documents);
        $this->assertArrayHasKey('trade_licence', $user->shop_documents);
        Storage::disk('public')->assertExists($user->shop_documents['nid']);
        Storage::disk('public')->assertExists($user->shop_documents['trade_licence']);

        // No free trial for shops: any signup quota is cleared so the seller
        // must buy a subscription before listing.
        $this->assertSame(0, (int) $user->ads_remaining);
        $this->assertNull($user->plan_expires_at);
    }

    public function test_shop_application_is_rejected_when_account_already_has_a_shop(): void
    {
        Mail::fake();
        Storage::fake('public');

        $user = User::factory()->create(['user_type' => 'seller', 'shop_name' => 'First Shop']);
        Sanctum::actingAs($user);

        $this->post('/api/v1/me/shop/apply', [
            'owner_name' => 'Rahim Uddin',
            'owner_phone' => '01700000000',
            'shop_name' => 'Second Shop',
            'shop_address' => 'Dhanmondi, Dhaka',
            'documents' => [
                'nid' => UploadedFile::fake()->create('nid.pdf', 200, 'application/pdf'),
                'trade_licence' => UploadedFile::fake()->create('trade-licence.pdf', 200, 'application/pdf'),
            ],
        ], ['Accept' => 'application/json'])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SHOP_ALREADY_OPEN');

        $this->assertSame('First Shop', $user->fresh()->shop_name);
    }
}
