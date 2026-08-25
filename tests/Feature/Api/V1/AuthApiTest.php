<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_wrapped_envelope_with_token(): void
    {
        $res = $this->postJson('/api/v1/auth/register', [
            'username' => 'alice',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'name' => 'Alice',
        ]);
        $res->assertCreated()
            ->assertJsonStructure(['data' => ['user' => ['id', 'username', 'email'], 'token']]);

        $this->assertDatabaseHas('user', ['email' => 'alice@example.com']);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::create([
            'username' => 'bob', 'email' => 'bob@x.com',
            'password_hash' => Hash::make('x'), 'status' => '1',
        ]);

        $res = $this->postJson('/api/v1/auth/register', [
            'username' => 'bob2', 'email' => 'bob@x.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $res->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');
    }

    public function test_login_and_me_and_logout(): void
    {
        User::create([
            'username' => 'charlie', 'email' => 'c@x.com',
            'password_hash' => Hash::make('secret123'),
            'status' => '1', 'group_id' => 'free',
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'c@x.com',
            'password' => 'secret123',
        ])->assertOk()->json();

        $token = $login['data']['token'];
        $this->assertNotEmpty($token);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.user.username', 'charlie');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        // Verify the token was actually revoked at the DB layer. (Re-hitting
        // /me in the same test process is unreliable because the HTTP kernel
        // caches the auth guard's user resolver across the two requests.)
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_login_rejects_bad_password(): void
    {
        User::create([
            'username' => 'dana', 'email' => 'd@x.com',
            'password_hash' => Hash::make('right'), 'status' => '1',
        ]);
        $this->postJson('/api/v1/auth/login', [
            'identifier' => 'd@x.com', 'password' => 'wrong',
        ])->assertStatus(401)->assertJsonPath('error.code', 'INVALID_CREDENTIALS');
    }
}
