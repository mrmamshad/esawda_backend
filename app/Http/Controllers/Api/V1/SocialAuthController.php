<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Social login (Google / Facebook) — driven by the frontend Bikroy-style
 * popup. The client OAuths against the provider directly, then POSTs the
 * resulting access-token to `/auth/social/{provider}/callback`. We verify
 * the token by fetching the userinfo endpoint (Google) or Graph API
 * (Facebook) and upsert a matching eShauda user, returning a fresh
 * Sanctum bearer.
 *
 * Zero third-party dependencies — plain Guzzle via `Http::` facade so we
 * don't have to `composer require laravel/socialite` in this workspace.
 */
class SocialAuthController extends Controller
{
    /** POST /auth/social/{provider}/callback  { access_token } */
    public function callback(Request $request, string $provider)
    {
        $data = $request->validate([
            'access_token' => ['required', 'string'],
        ]);

        $profile = match ($provider) {
            'google'   => $this->fetchGoogle($data['access_token']),
            'facebook' => $this->fetchFacebook($data['access_token']),
            default    => throw ValidationException::withMessages(['provider' => "Unsupported provider: {$provider}"]),
        };

        // Upsert by email so returning users keep the same account regardless
        // of which social provider they last used.
        $user = User::where('email', $profile['email'])->first();
        if (! $user) {
            $user = User::create([
                'username'      => $this->uniqueUsername($profile['email']),
                'email'         => $profile['email'],
                'name'          => $profile['name'] ?? Str::before($profile['email'], '@'),
                'password_hash' => Hash::make(Str::random(40)),   // never used — social only
                'status'        => '1',
                'group_id'      => 'free',
                'user_type'     => 'user',
                'image'         => $profile['avatar_url'] ?? 'default_user.png',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        return response()->json([
            'data' => [
                'user'  => (new UserResource($user))->resolve(),
                'token' => $user->createToken('social:' . $provider)->plainTextToken,
            ],
        ]);
    }

    /* -- provider fetchers -- */

    /** @return array{email:string,name:?string,avatar_url:?string} */
    private function fetchGoogle(string $token): array
    {
        $res = Http::withToken($token)->get('https://www.googleapis.com/oauth2/v3/userinfo');
        if (! $res->ok()) {
            Log::warning('google userinfo failed', ['status' => $res->status(), 'body' => $res->body()]);
            throw ValidationException::withMessages(['access_token' => 'Google token rejected.']);
        }
        $j = $res->json();
        if (empty($j['email'])) {
            throw ValidationException::withMessages(['access_token' => 'Google token did not include an email address.']);
        }
        return [
            'email'      => $j['email'],
            'name'       => $j['name']    ?? null,
            'avatar_url' => $j['picture'] ?? null,
        ];
    }

    /** @return array{email:string,name:?string,avatar_url:?string} */
    private function fetchFacebook(string $token): array
    {
        $res = Http::get('https://graph.facebook.com/me', [
            'access_token' => $token,
            'fields'       => 'id,name,email,picture.type(large)',
        ]);
        if (! $res->ok()) {
            Log::warning('facebook graph failed', ['status' => $res->status(), 'body' => $res->body()]);
            throw ValidationException::withMessages(['access_token' => 'Facebook token rejected.']);
        }
        $j = $res->json();
        if (empty($j['email'])) {
            throw ValidationException::withMessages(['access_token' => 'Facebook did not return an email — please grant email permission.']);
        }
        return [
            'email'      => $j['email'],
            'name'       => $j['name'] ?? null,
            'avatar_url' => $j['picture']['data']['url'] ?? null,
        ];
    }

    private function uniqueUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '_') ?: 'user';
        $u    = $base;
        $i    = 0;
        while (DB::table('users')->where('username', $u)->exists()) {
            $u = $base . '_' . (++$i);
        }
        return $u;
    }
}
