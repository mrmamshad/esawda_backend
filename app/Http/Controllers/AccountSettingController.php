<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Legacy: php/account-setting.php. Combines profile edit + password change
 * in a single page — the two forms POST with an `_action` discriminator.
 */
class AccountSettingController extends Controller
{
    public function __construct(private AuthService $auth, private ThemeRenderer $theme) {}

    public function index(Request $request)
    {
        if (! $this->auth->check($request)) return redirect()->route('auth.login');

        $user = User::findOrFail(session('user.id'));

        if ($request->isMethod('post')) {
            if ($request->input('_action') === 'profile') {
                $data = $request->validate([
                    'name'        => 'nullable|string|max:225',
                    'email'       => 'required|email',
                    'phone'       => 'nullable|string|max:50',
                    'description' => 'nullable|string',
                    'country'     => 'nullable|string|max:50',
                    'city'        => 'nullable|string|max:225',
                ]);
                $user->fill($data);
                $user->updated_at = now();
                $user->save();
                session()->flash('flash_success', 'Profile updated.');
                return back();
            }

            if ($request->input('_action') === 'password') {
                $data = $request->validate([
                    'current_password' => 'required|string',
                    'password'         => 'required|string|min:6|confirmed',
                ]);
                if (! Hash::check($data['current_password'], $user->password_hash)) {
                    return back()->withErrors(['current_password' => 'Current password is incorrect.']);
                }
                $user->password_hash = Hash::make($data['password']);
                $user->updated_at    = now();
                $user->save();
                session()->flash('flash_success', 'Password changed.');
                return back();
            }
        }

        return $this->theme->render('account-setting', ['user' => $user]);
    }
}
