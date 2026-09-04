<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureLegacyLogin;
use App\Http\Middleware\SetLocale;
use App\Providers\AuthServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'quickad.auth' => EnsureLegacyLogin::class,
            'admin' => EnsureAdmin::class,
        ]);
        $middleware->web(append: [SetLocale::class]);
    })
    ->withProviders([
        AuthServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // Uniform JSON error envelope for /api/* requests. Web + Filament
        // responses fall through to Laravel defaults.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null;
            }
            // Unwrap Laravel's HttpException layer that some auth paths use
            // (e.g. Gate::authorize() in Laravel 11 wraps AuthorizationException
            // into an HttpException before reaching this handler).
            $prev = $e->getPrevious();

            [$code, $message, $status, $fields] = match (true) {
                $e instanceof ValidationException => ['VALIDATION_FAILED', $e->getMessage(), 422, $e->errors()],
                $e instanceof AuthenticationException => ['UNAUTHENTICATED', 'Authentication required.', 401, []],
                $e instanceof AuthorizationException,
                $prev instanceof AuthorizationException => ['FORBIDDEN', $e->getMessage() ?: 'Forbidden.', 403, []],
                $e instanceof AccessDeniedHttpException => ['FORBIDDEN', $e->getMessage() ?: 'Forbidden.', 403, []],
                $e instanceof NotFoundHttpException,
                $e instanceof ModelNotFoundException => ['NOT_FOUND', 'Resource not found.', 404, []],
                $e instanceof MethodNotAllowedHttpException => ['METHOD_NOT_ALLOWED', 'HTTP method not allowed.', 405, []],
                $e instanceof ThrottleRequestsException => ['RATE_LIMITED', 'Too many requests.', 429, []],
                // Unauthenticated non-JSON hits never redirect to a login
                // page (there is no `login` route) — answer 401 like the
                // JSON path instead of a 500 RouteNotFoundException.
                $e instanceof RouteNotFoundException => ['UNAUTHENTICATED', 'Authentication required.', 401, []],
                $e instanceof HttpException => ['HTTP_ERROR', $e->getMessage() ?: 'HTTP error.', $e->getStatusCode(), []],
                default => ['SERVER_ERROR', config('app.debug') ? $e->getMessage() : 'Server error.', 500, []],
            };
            $body = ['error' => ['code' => $code, 'message' => $message]];
            if ($fields) {
                $body['error']['fields'] = $fields;
            }

            return response()->json($body, $status);
        });
    })->create();
