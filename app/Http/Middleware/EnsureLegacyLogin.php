<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;

/**
 * Route middleware that matches the legacy `checkloggedin()` guard.
 * Redirects to /login when the session is missing or tampered.
 */
class EnsureLegacyLogin
{
    public function __construct(private AuthService $auth) {}

    public function handle(Request $request, Closure $next)
    {
        if (!$this->auth->check($request)) {
            return redirect()->route('auth.login')
                ->with('flash_error', 'Please login to continue.');
        }

        return $next($request);
    }
}
