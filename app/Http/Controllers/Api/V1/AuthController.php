<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ForgotPasswordRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Requests\V1\ResetPasswordRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\EmailQueue;
use App\Models\User;
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
            'username'      => $data['username'],
            'email'         => $data['email'],
            'name'          => $data['name']  ?? $data['username'],
            'phone'         => $data['phone'] ?? null,
            'password_hash' => Hash::make($data['password']),
            'status'        => '1',
            'group_id'      => 'free',
            'user_type'     => 'user',
            'image'         => 'default_user.png',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return $this->created([
            'user'  => (new UserResource($user))->resolve(),
            'token' => $user->createToken('spa')->plainTextToken,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $data  = $request->validated();
        $field = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        /** @var User|null $user */
        $user = User::where($field, $data['identifier'])->first();
        if (! $user || ! Hash::check($data['password'], (string) $user->password_hash)) {
            return $this->error('INVALID_CREDENTIALS', 'Email or password is incorrect.', 401);
        }
        if ((string) $user->status === '0') {
            return $this->error('ACCOUNT_DISABLED', 'Your account is disabled.', 403);
        }

        $user->forceFill(['lastactive' => now(), 'online' => '1'])->save();

        return $this->ok([
            'user'  => (new UserResource($user))->resolve(),
            'token' => $user->createToken($data['device'] ?? 'spa')->plainTextToken,
        ]);
    }

    public function me(Request $request)
    {
        return $this->ok(['user' => (new UserResource($request->user()))->resolve()]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->ok(['message' => 'Logged out.']);
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
            $user->forceFill(['forgot' => $token])->save();

            $resetUrl = rtrim(env('FRONTEND_URLS', 'http://localhost:3000'), ',')
                      . '/auth/reset?token=' . urlencode($token);

            // Enqueue via the legacy email queue table.
            EmailQueue::create([
                'email'   => $user->email,
                'toname'  => $user->name ?: $user->username,
                'subject' => 'Reset your offersale. password',
                'body'    => "Hi {$user->name},\n\nUse the link below to reset your password:\n{$resetUrl}\n\nIf you did not request this, ignore this email.",
            ]);
        }
        return $this->ok(['message' => 'If the email exists, a reset link has been sent.']);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $user = User::where('forgot', $data['token'])->first();
        if (! $user) {
            return $this->error('INVALID_TOKEN', 'Reset token is invalid or has been used.', 422);
        }

        DB::transaction(function () use ($user, $data) {
            $user->forceFill([
                'password_hash' => Hash::make($data['password']),
                'forgot'        => null,
                'updated_at'    => now(),
            ])->save();
            $user->tokens()->delete(); // revoke every existing session
        });

        return $this->ok([
            'user'  => (new UserResource($user))->resolve(),
            'token' => $user->createToken('spa')->plainTextToken,
        ]);
    }
}
