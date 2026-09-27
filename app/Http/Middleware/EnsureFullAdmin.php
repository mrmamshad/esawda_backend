<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards admin areas that a "limited" admin must not access (Users,
 * Transactions). Runs after auth:sanctum + admin, so the caller is already a
 * verified admin — here we additionally reject limited admins.
 */
class EnsureFullAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isLimitedAdmin()) {
            abort(403, 'This section is restricted for your role.');
        }

        return $next($request);
    }
}
