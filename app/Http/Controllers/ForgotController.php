<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * Legacy `php/login.php` forgot-password subflow. GET ?token=... shows
 * the reset form, POST with new password commits it.
 */
class ForgotController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function index(Request $request)
    {
        if ($request->isMethod('post') && $request->filled('token')) {
            $data = $request->validate([
                'token' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);
            $user = $this->auth->resetPassword($data['token'], $data['password']);
            if ($user) {
                session()->flash('flash_success', 'Password updated — please log in.');

                return redirect()->route('auth.login');
            }
            session()->flash('flash_error', 'Invalid or expired reset link.');
        }

        return view('placeholder', ['legacy' => 'login.php', 'action' => 'forgot']);
    }
}
