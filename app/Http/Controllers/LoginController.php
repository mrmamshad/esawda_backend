<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * Legacy `php/login.php`. Handles POST login + forgot-password intake.
 */
class LoginController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function index(Request $request)
    {
        if ($this->auth->check($request)) {
            return redirect()->route('dashboard');
        }

        // POST login
        if ($request->isMethod('post') && $request->filled('submit')) {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = $this->auth->attempt(
                $request->input('username'),
                $request->input('password'),
                $request
            );

            if ($user) {
                // Legacy remember-me cookie: `qurm = userId.sha512Hash`
                if ($request->filled('remember')) {
                    $login = hash('sha512', $user->password_hash . $request->userAgent());
                    cookie()->queue('qurm', $user->id.'.'.$login, 60 * 24 * 30);
                }
                return redirect()->intended(route('dashboard'));
            }

            return back()->withErrors(['login' => 'Invalid credentials or account not active.'])
                         ->withInput($request->except('password'));
        }

        // Forgot-password intake (legacy uses ?forgot=1 with a token)
        if ($request->filled('forgot') && $request->filled('email')) {
            $out = $this->auth->makeForgotToken($request->input('email'));
            if ($out) {
                // TODO(migration): dispatch Mail::to()->send(new ForgotPasswordMail($out['token']))
                session()->flash('flash_success', 'A password reset link has been sent to your email.');
            } else {
                session()->flash('flash_error', 'No account matches that email.');
            }
            return back();
        }

        return app(\App\Services\ThemeRenderer::class)->render('login');
    }
}
