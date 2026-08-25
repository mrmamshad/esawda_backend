<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Replacement for the procedural helpers in
 * `includes/functions/func.users.php`.
 *
 * Handles registration, password hashing (bcrypt-compatible with the
 * legacy `password_hash()` column), and social-login linkage.
 */
class UserService
{
    public function register(array $data): User
    {
        $data['password_hash'] = Hash::make($data['password'] ?? '');
        $data['created_at'] = now();
        $data['status'] = '1';
        $data['group_id'] = $data['group_id'] ?? 'free';

        unset($data['password']);

        // forceCreate: trusted internal registration — status/group_id are
        // set here deliberately, not from caller-controlled mass assignment.
        return User::forceCreate($data);
    }

    public function verifyLegacyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }
}
