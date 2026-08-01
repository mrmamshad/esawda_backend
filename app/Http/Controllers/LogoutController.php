<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function index(Request $request)
    {
        $this->auth->logout($request);
        return redirect()->route('auth.login')
                         ->with('flash_success', 'You have been logged out.');
    }
}
