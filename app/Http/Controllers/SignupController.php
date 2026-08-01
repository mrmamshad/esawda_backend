<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Legacy `php/signup.php`. Handles registration + email confirmation.
 */
class SignupController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function index(Request $request)
    {
        if ($this->auth->check($request)) {
            return redirect()->route('dashboard');
        }

        // Email-confirm link handler (legacy GET ?confirm=TOKEN)
        if ($request->filled('confirm')) {
            $user = $this->auth->confirmSignup($request->query('confirm'));
            if ($user) {
                session()->flash('flash_success', 'Email confirmed — please log in.');
                return redirect()->route('auth.login');
            }
            session()->flash('flash_error', 'Invalid or expired confirmation link.');
            return redirect()->route('auth.login');
        }

        // Ajax username-availability check (legacy POST ?ajax=uname)
        if ($request->isMethod('post') && $request->query('ajax') === 'uname') {
            return response()->json([
                'available' => ! $this->auth->usernameExists($request->input('username', '')),
            ]);
        }

        // Registration
        if ($request->isMethod('post') && $request->filled('submit')) {
            $data = $request->validate([
                'username'  => ['required', 'string', 'min:3', 'max:40', 'regex:/^[A-Za-z0-9_.-]+$/',
                                Rule::unique('user', 'username')],
                'email'     => ['required', 'email', Rule::unique('user', 'email')],
                'password'  => 'required|string|min:6|confirmed',
                'name'      => 'nullable|string|max:225',
                'user_type' => 'nullable|in:user,seller',
            ]);
            $out = $this->auth->register($data);
            // TODO(migration): Mail::to($out['user']->email)->send(new SignupConfirmMail($out['confirm_token']))
            session()->flash('flash_success', 'Registration successful — check your email to confirm.');
            return redirect()->route('auth.login');
        }

        return app(\App\Services\ThemeRenderer::class)->render('signup');
    }
}
