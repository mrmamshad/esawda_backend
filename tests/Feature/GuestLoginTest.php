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

        $seller = User::forceCreate([
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

    public function test_guest_register_creates_new_account_with_quota(): void
    {
        $res = $this->postJson('/api/v1/auth/guest-register', [
            'name' => 'New Poster',
            'mobile' => '01755555555',
            'password' => 'secret123',
        ]);

        $res->assertOk()->assertJsonStructure(['data' => ['user' => ['id'], 'token']]);

        $user = User::where('phone', '01755555555')->firstOrFail();
        $this->assertTrue((int) $user->ads_remaining > 0);
    }

    public function test_guest_register_existing_phone_returns_409_and_keeps_password(): void
    {
        $victim = User::forceCreate([
            'username' => 'victim-user',
            'email' => 'victim@example.com',
            'name' => 'Victim',
            'phone' => '01766666666',
            'password_hash' => bcrypt('original-pass'),
            'status' => '1',
            'group_id' => 'free',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $originalHash = $victim->password_hash;

        // Account-takeover attempt: must NOT reset the password or log in.
        $this->postJson('/api/v1/auth/guest-register', [
            'name' => 'Attacker',
            'mobile' => '01766666666',
            'password' => 'attacker-pass',
        ])->assertStatus(409)
            ->assertJsonPath('error.code', 'ACCOUNT_EXISTS');

        $this->assertSame($originalHash, $victim->fresh()->password_hash);
        $this->assertSame(1, User::where('phone', '01766666666')->count());
    }

    public function test_guest_login_refuses_email_registered_account(): void
    {
        User::forceCreate([
            'username' => 'real-user',
            'email' => 'real@example.com',
            'name' => 'Real',
            'phone' => '01777777777',
            'password_hash' => bcrypt('secret123'),
            'status' => '1',
            'group_id' => 'free',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Passwordless token for a real account would be impersonation.
        $this->postJson('/api/v1/auth/guest-login', [
            'name' => 'Anyone',
            'mobile' => '01777777777',
        ])->assertStatus(409)
            ->assertJsonPath('error.code', 'ACCOUNT_EXISTS');
    }
}
