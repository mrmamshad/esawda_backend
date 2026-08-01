<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards /api/v1/admin/* routes. The caller must already be authenticated
 * via Sanctum (auth:sanctum runs first), so here we just verify that the
 * user record has an admin-tier user_type.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) abort(401, 'Unauthenticated.');

        // Two ways to be admin:
        //   1. Legacy Bylancer schema: a row in `admins` table matching this
        //      user's email / username.
        //   2. Explicit flag on the `user` row (user_type = 'admin' — used
        //      when running the CHECK-constraint-relaxed schema).
        if (! $user->isAdmin()) abort(403, 'Admin access required.');
        return $next($request);
    }
}
