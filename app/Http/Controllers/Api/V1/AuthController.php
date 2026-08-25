<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ForgotPasswordRequest;
use App\Http\Requests\V1\GuestLoginRequest;
use App\Http\Requests\V1\GuestRegisterRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Requests\V1\ResetPasswordRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\Option;
use App\Models\User;
use App\Services\Mail\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * /api/v1/auth/*
 *
 * Sanctum bearer tokens, JSON-only. No cookie sessions in the SPA
 * separate-origin model. All responses use the { data, meta } envelope
 * from the Controller base's ApiResponses trait.
 */
class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::forceCreate([
            'username' => $data['username'],
            'email' => $data['email'],
            'name' => $data['name'] ?? $data['username'],
            'phone' => $data['phone'] ?? null,
            'password_hash' => Hash::make($data['password']),
            'status' => '1',
            'group_id' => 'free',
            'user_type' => 'user',
            'image' => 'default_user.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $token = $user->createToken('spa')->plainTextToken;

        return $this->withAuthCookie($this->created([
            'user' => (new UserResource($user))->resolve(),
            'token' => $token,
        ]), $token);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $id = $data['identifier'];

        // Accept email, username, or phone — guest accounts are created
        // with a phone, so "phone + password" must just work. Several users
        // may share one phone (a guest + a seller), so pick the account whose
        // password actually matches rather than the first row.
        $candidates = User::where(function ($q) use ($id) {
            $q->where('email', $id)->orWhere('username', $id)->orWhere('phone', $id);
        })->get();
        $user = $candidates->first(fn ($u) => Hash::check($data['password'], (string) $u->password_hash));
        if (!$user) {
            return $this->error('INVALID_CREDENTIALS', 'Email or password is incorrect.', 401);
        }
        if ((string) $user->status === '0') {
            return $this->error('ACCOUNT_DISABLED', 'Your account is disabled.', 403);
        }

        $user->forceFill(['lastactive' => now(), 'online' => '1'])->save();

        $token = $user->createToken($data['device'] ?? 'spa')->plainTextToken;

        return $this->withAuthCookie($this->ok([
            'user' => (new UserResource($user))->resolve(),
            'token' => $token,
        ]), $token);
    }

    public function me(Request $request)
    {
        return $this->ok(['user' => (new UserResource($request->user()))->resolve()]);
    }

    /**
     * Lightweight guest session — lets a buyer message a seller (or post a
     * quick enquiry) without a full email/password registration.
     *
     * Reuses an existing user whose phone matches, otherwise creates a fresh
     * guest account (no real password — a random one is stored so the row can
     * never be used for a password login). Throttled like the other auth
     * endpoints to blunt abuse.
     */
    public function guestLogin(GuestLoginRequest $request)
    {
        $data = $request->validated();
        $mobile = trim((string) $data['mobile']);
        $name = trim((string) $data['name']);

        /** @var User|null $user */
        $user = User::where('phone', $mobile)->first();

        if (!$user) {
            $user = User::forceCreate([
                'username' => $this->uniqueGuestUsername($mobile),
                'name' => $name,
                'phone' => $mobile,
                'password_hash' => Hash::make(Str::random(64)),
                'status' => '1',
                'group_id' => 'free',
                'user_type' => 'user',
                'image' => 'default_user.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ((string) $user->status === '0') {
            return $this->error('ACCOUNT_DISABLED', 'This account is disabled.', 403);
        }

        $user->forceFill(['lastactive' => now(), 'online' => '1'])->save();

        $token = $user->createToken($data['device'] ?? 'guest')->plainTextToken;

        return $this->withAuthCookie($this->ok([
            'user' => (new UserResource($user))->resolve(),
            'token' => $token,
        ]), $token);
    }

    /**
     * Guest account with a real password — the "Post a Product" sign-up.
     * Buyer enters name + phone + password once, straight from the product
     * form. Reuses an existing user whose phone matches (idempotent: resets
     * their password and returns a fresh token), otherwise creates the row.
     *
     * New guest accounts get a small free listing quota (guest_ads_quota,
     * admin-tunable) so the first few posts are free. grantGuestAds() is
     * a no-op if the user already holds quota.
     */
    public function guestRegister(GuestRegisterRequest $request)
    {
        $data = $request->validated();
        $mobile = trim((string) $data['mobile']);
        $name = trim((string) $data['name']);

        /** @var User|null $user */
        $user = User::where('phone', $mobile)->first();

        if (!$user) {
            $user = User::forceCreate([
                'username' => $this->uniqueGuestUsername($mobile),
                'name' => $name,
                'phone' => $mobile,
                'password_hash' => Hash::make($data['password']),
                'status' => '1',
                'group_id' => 'free',
                'user_type' => 'user',
                'image' => 'default_user.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->grantGuestAds($user);
        } elseif ((string) $user->status === '0') {
            return $this->error('ACCOUNT_DISABLED', 'This account is disabled.', 403);
        }

        $user->forceFill([
            'password_hash' => Hash::make($data['password']),
            'lastactive' => now(),
            'online' => '1',
        ])->save();

        $token = $user->createToken($data['device'] ?? 'guest')->plainTextToken;

        return $this->withAuthCookie($this->ok([
            'user' => (new UserResource($user))->resolve(),
            'token' => $token,
        ]), $token);
    }

    /**
     * First-time listing allowance for new guest accounts. Quota lives in
     * the `options` table (guest_ads_quota, default 5) so admins can tune
     * it without a migration. No-op once the user has any future-dated plan.
     */
    private function grantGuestAds(User $user): void
    {
        $quota = (int) (Option::where('option_name', 'guest_ads_quota')->value('option_value') ?? 5);
        if ($quota <= 0) {
            return;
        }

        if ($user->plan_expires_at?->isFuture() && (int) $user->ads_remaining >= $quota) {
            return;
        }

        $user->forceFill([
            'plan_expires_at' => $user->plan_expires_at?->isFuture()
                ? $user->plan_expires_at
                : now()->addDays(30),
            'ads_remaining' => (int) $user->ads_remaining + $quota,
            'updated_at' => now(),
        ])->save();
    }

    private function uniqueGuestUsername(string $mobile): string
    {
        $base = 'guest'.preg_replace('/\D+/', '', $mobile);
        $base = $base ?: 'guestuser';
        $base = substr($base, 0, 40);
        $candidate = $base;
        $i = 1;
        while (User::where('username', $candidate)->exists()) {
            $candidate = substr($base, 0, 34).'_'.$i;
            $i++;
        }

        return $candidate;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->withClearedAuthCookie($this->ok(['message' => 'Logged out.']));
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->ok(['message' => 'All sessions revoked.']);
    }

    public function forgot(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->string('email'))->first();
        // Always 200 to prevent user enumeration.
        if ($user) {
            $token = Str::random(64);
            $user->forceFill([
                'forgot' => $token,
                'forgot_expires_at' => now()->addMinutes(60),
            ])->save();

            $resetUrl = rtrim(env('FRONTEND_URLS', 'http://localhost:3000'), ',')
                      .'/auth/reset?token='.urlencode($token);

            app(MailService::class)->passwordReset($user, $resetUrl);
        }

        return $this->ok(['message' => 'If the email exists, a reset link has been sent.']);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $user = User::where('forgot', $data['token'])->first();
        if (!$user) {
            return $this->error('INVALID_TOKEN', 'Reset token is invalid or has been used.', 422);
        }

        // Token expires 60 minutes after issue; clear it on use (single-use).
        if ($user->forgot_expires_at && now()->greaterThan($user->forgot_expires_at)) {
            $user->forceFill(['forgot' => null, 'forgot_expires_at' => null])->save();

            return $this->error('INVALID_TOKEN', 'Reset token has expired.', 422);
        }

        DB::transaction(function () use ($user, $data) {
            $user->forceFill([
                'password_hash' => Hash::make($data['password']),
                'forgot' => null,
                'forgot_expires_at' => null,
                'updated_at' => now(),
            ])->save();
            $user->tokens()->delete(); // revoke every existing session
        });

        $token = $user->createToken('spa')->plainTextToken;

        return $this->withAuthCookie($this->ok([
            'user' => (new UserResource($user))->resolve(),
            'token' => $token,
        ]), $token);
    }
}
