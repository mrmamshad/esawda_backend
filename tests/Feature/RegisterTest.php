<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Signup User',
            'email' => 'signup@example.com',
            'phone' => '01712345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_register_accepts_mobile_without_username(): void
    {
        $res = $this->postJson('/api/v1/auth/register', $this->payload());

        $res->assertCreated()
            ->assertJsonPath('data.user.phone', '01712345678')
            ->assertJsonStructure(['data' => ['user' => ['id', 'username'], 'token']]);

        $user = User::where('phone', '01712345678')->firstOrFail();
        $this->assertNotEmpty($user->username);
    }

    public function test_register_rejects_invalid_mobile(): void
    {
        $this->postJson('/api/v1/auth/register', $this->payload(['phone' => '12345']))
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonPath('error.fields.phone.0', 'Enter a valid 11-digit Bangladeshi mobile number, e.g. 01712345678.');

        $this->postJson('/api/v1/auth/register', $this->payload(['phone' => '02712345678']))
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');

        $this->assertDatabaseCount('user', 0);
    }

    public function test_register_rejects_taken_mobile_with_conflict(): void
    {
        $this->postJson('/api/v1/auth/register', $this->payload())->assertCreated();

        $this->postJson('/api/v1/auth/register', $this->payload(['email' => 'other@example.com']))
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'ACCOUNT_EXISTS');
    }

    public function test_register_still_accepts_explicit_username(): void
    {
        $this->postJson('/api/v1/auth/register', $this->payload(['username' => 'customname']))
            ->assertCreated();

        $this->assertDatabaseHas('user', ['phone' => '01712345678', 'username' => 'customname']);
    }
}
