<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Mobile/SPA authentication API.
 * Endpoints:
 *   POST /api/v1/auth/register
 *   POST /api/v1/auth/login
 *   GET  /api/v1/auth/me       (Sanctum-guarded)
 *   POST /api/v1/auth/logout   (Sanctum-guarded)
 */
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:40', Rule::unique('user', 'username')],
            'email'    => ['required', 'email', Rule::unique('user', 'email')],
            'password' => 'required|string|min:6',
            'name'     => 'nullable|string|max:225',
        ]);
        $user = User::create([
            'username'      => $data['username'],
            'email'         => $data['email'],
            'name'          => $data['name'] ?? $data['username'],
            'password_hash' => Hash::make($data['password']),
            'status'        => '1',
            'group_id'      => 'free',
            'user_type'     => 'user',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        $token = $user->createToken('mobile-app')->plainTextToken;
        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|string',   // accepts email OR username
            'password' => 'required|string',
        ]);
        $field = filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user  = User::where($field, $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('mobile-app')->plainTextToken;
        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
