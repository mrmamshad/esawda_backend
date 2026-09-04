<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** Build a request that Laravel's session helper can attach a store to. */
    private function makeReq(): Request
    {
        $req = Request::create('/', 'GET');
        $req->setLaravelSession(app('session.store'));

        return $req;
    }

    public function test_login_with_legacy_password_hash(): void
    {
        $u = User::forceCreate([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => Hash::make('secret123'),
            'status' => '1',
            'group_id' => 'free',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = app(AuthService::class)->attempt('test@example.com', 'secret123', $this->makeReq());
        $this->assertNotNull($user);
        $this->assertSame($u->id, $user->id);
    }

    public function test_login_rejects_wrong_password(): void
    {
        User::forceCreate([
            'username' => 'a',
            'email' => 'a@a.com',
            'password_hash' => Hash::make('right'),
            'status' => '1',
        ]);
        $this->assertNull(app(AuthService::class)->attempt('a@a.com', 'wrong', $this->makeReq()));
    }

    public function test_email_and_username_existence_checks(): void
    {
        $svc = app(AuthService::class);
        $this->assertFalse($svc->emailExists('nobody@nope.com'));
        $this->assertFalse($svc->usernameExists('nobody'));
        User::forceCreate(['username' => 'x', 'email' => 'x@x.com', 'password_hash' => Hash::make('p'), 'status' => '1']);
        $this->assertTrue($svc->emailExists('x@x.com'));
        $this->assertTrue($svc->usernameExists('x'));
    }
}
