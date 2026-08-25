<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class GuestLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_login_creates_guest_user(): void
    {
        $res = $this->postJson('/api/v1/auth/guest-login', [
            'name' => 'Guest Buyer',
            'mobile' => '01711111111',
        ]);

        $res->assertOk()
            ->assertJsonPath('data.user.phone', '01711111111')
            ->assertJsonPath('data.user.name', 'Guest Buyer')
            ->assertJsonStructure(['data' => ['user' => ['id'], 'token']]);

        $this->assertDatabaseHas('user', ['phone' => '01711111111', 'name' => 'Guest Buyer']);
    }

    public function test_guest_login_reuses_existing_mobile(): void
    {
        $this->postJson('/api/v1/auth/guest-login', ['name' => 'First', 'mobile' => '01722222222'])->assertOk();
        $first = User::where('phone', '01722222222')->firstOrFail();

        $res = $this->postJson('/api/v1/auth/guest-login', ['name' => 'Second', 'mobile' => '01722222222']);

        // Reuse the existing account (id + stored name) — a returning guest
        // is not treated as a brand-new identity just because they typed a
        // different name this time.
        $res->assertOk()
            ->assertJsonPath('data.user.id', $first->id)
            ->assertJsonPath('data.user.name', 'First');

        $this->assertSame(1, User::where('phone', '01722222222')->count());
    }

    public function test_guest_login_requires_name_and_mobile(): void
    {
        $this->postJson('/api/v1/auth/guest-login', [])->assertStatus(422);
        $this->postJson('/api/v1/auth/guest-login', ['name' => 'Only Name'])->assertStatus(422);
        $this->postJson('/api/v1/auth/guest-login', ['mobile' => '01733333333'])->assertStatus(422);
    }

    public function test_guest_user_can_send_message_to_seller(): void
    {
        Event::fake([MessageSent::class]);

        $seller = User::create([
            'username' => 'seller-guest-test',
            'email' => 'seller-guest-test@example.com',
            'name' => 'Seller',
            'password_hash' => bcrypt('secret123'),
            'status' => '1',
            'group_id' => 'free',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $login = $this->postJson('/api/v1/auth/guest-login', [
            'name' => 'Guest',
            'mobile' => '01744444444',
        ])->assertOk()->json('data');
        $token = $login['token'];

        $res = $this->withToken($token)->postJson('/api/v1/messages', [
            'to' => $seller->id,
            'body' => 'Hello, is this still available?',
        ]);

        $res->assertCreated()
            ->assertJsonPath('data.to_id', $seller->id);
    }
}
