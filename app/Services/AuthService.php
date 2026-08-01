<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Faithful port of the legacy `includes/functions/func.users.php`
 * authentication helpers: `userlogin()`, `checkloggedin()`,
 * `create_user_session()`, `checkbrute()`, `check_account_exists()`,
 * `check_username_exists()`.
 *
 * The legacy code uses `password_hash()` / `password_verify()` (bcrypt),
 * so the hashes are 100% compatible with Laravel's `Hash::check()`.
 * That means no rehash-on-login migration is needed — logins from the
 * legacy DB just work.
 */
class AuthService
{
    private const MAX_BAD_ATTEMPTS = 5;
    private const BRUTE_WINDOW_SECONDS = 2 * 60 * 60; // 2 hours

    /**
     * Legacy: `userlogin($email, $password)`.
     * Returns the authenticated User or null.
     */
    public function attempt(string $emailOrUsername, string $password, Request $request): ?User
    {
        $field = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $emailOrUsername)->first();
        if (! $user) {
            return null;
        }

        if ($this->isBruteForced($user->id)) {
            return null;
        }

        if (! Hash::check($password, $user->password_hash)) {
            DB::table('login_attempts')->insert([
                'user_id' => $user->id,
                'time'    => (string) time(),
            ]);
            return null;
        }

        $this->createSession($user, $request);
        return $user;
    }

    /**
     * Legacy: `create_user_session()`. Stores id / username /
     * login_string (`sha512(password_hash . userAgent)`) in the session
     * so `checkloggedin()` can validate on every request.
     */
    public function createSession(User $user, Request $request): void
    {
        $userAgent   = (string) $request->userAgent();
        $loginString = hash('sha512', $user->password_hash . $userAgent);

        $request->session()->put('user', [
            'id'            => $user->id,
            'username'      => $user->username,
            'login_string'  => $loginString,
        ]);

        // Laravel's built-in guard — keeps `Auth::user()` working too.
        auth()->login($user);

        // Legacy also updates `lastactive` on login.
        $user->lastactive = now();
        $user->save();
    }

    /** Legacy: `checkloggedin()`. */
    public function check(Request $request): bool
    {
        $sess = $request->session()->get('user');
        if (! $sess || empty($sess['id']) || empty($sess['login_string'])) {
            return $this->checkPersistentCookie($request);
        }

        $user = User::find($sess['id']);
        if (! $user) {
            return false;
        }

        $expected = hash('sha512', $user->password_hash . (string) $request->userAgent());
        return hash_equals($expected, $sess['login_string']);
    }

    /** Legacy uses a `qurm` remember-me cookie: `userId.sha512Hash`. */
    private function checkPersistentCookie(Request $request): bool
    {
        $raw = $request->cookie('qurm');
        if (! $raw) {
            return false;
        }
        [$id, $hash] = array_pad(explode('.', $raw, 2), 2, null);
        if (! $id || ! $hash) {
            return false;
        }
        $user = User::find($id);
        if (! $user) {
            return false;
        }
        $expected = hash('sha512', $user->password_hash . (string) $request->userAgent());
        if (hash_equals($expected, $hash)) {
            $this->createSession($user, $request);
            return true;
        }
        return false;
    }

    /** Legacy: `checkbrute()`. */
    public function isBruteForced(int $userId): bool
    {
        $threshold = time() - self::BRUTE_WINDOW_SECONDS;
        $count = DB::table('login_attempts')
            ->where('user_id', $userId)
            ->where('time', '>', (string) $threshold)
            ->count();
        return $count > self::MAX_BAD_ATTEMPTS;
    }

    /** Legacy: `check_account_exists()`. */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /** Legacy: `check_username_exists()`. */
    public function usernameExists(string $username): bool
    {
        return User::where('username', $username)->exists();
    }

    /**
     * Legacy: signup flow from `php/signup.php`. Creates a pending
     * user (`status = 0`) and returns a confirmation token that the
     * caller emails to the user.
     */
    public function register(array $data): array
    {
        $confirm = Str::random(32);
        $user = User::create([
            'group_id'      => 'free',
            'user_type'     => $data['user_type'] ?? 'user',
            'username'      => $data['username'],
            'email'         => $data['email'],
            'name'          => $data['name'] ?? $data['username'],
            'password_hash' => Hash::make($data['password']),
            'confirm'       => $confirm,
            'status'        => '0',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        return ['user' => $user, 'confirm_token' => $confirm];
    }

    /** Called via `/signup?confirm=...` — activates the account. */
    public function confirmSignup(string $token): ?User
    {
        $user = User::where('confirm', $token)->first();
        if (! $user) return null;
        $user->status  = '1';
        $user->confirm = null;
        $user->save();
        return $user;
    }

    /** Legacy: `send_forgot_email` prep — set forgot token, caller mails it. */
    public function makeForgotToken(string $email): ?array
    {
        $user = User::where('email', $email)->first();
        if (! $user) return null;
        $user->forgot = Str::random(40);
        $user->save();
        return ['user' => $user, 'token' => $user->forgot];
    }

    public function resetPassword(string $token, string $newPassword): ?User
    {
        $user = User::where('forgot', $token)->first();
        if (! $user) return null;
        $user->password_hash = Hash::make($newPassword);
        $user->forgot        = null;
        $user->updated_at    = now();
        $user->save();
        return $user;
    }

    public function logout(Request $request): void
    {
        $request->session()->forget(['user', 'token', 'chatHistory', 'openChatBoxes']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        auth()->logout();
        cookie()->queue(cookie()->forget('qurm'));
    }
}
