<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
            'quickad.auth' => \App\Http\Middleware\EnsureLegacyLogin::class,
            'admin'        => \App\Http\Middleware\EnsureAdmin::class,
        ]);
        $middleware->web(append: [\App\Http\Middleware\SetLocale::class]);
    })
    ->withProviders([
        \App\Providers\AuthServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // Uniform JSON error envelope for /api/* requests. Web + Filament
        // responses fall through to Laravel defaults.
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }
            // Unwrap Laravel's HttpException layer that some auth paths use
            // (e.g. Gate::authorize() in Laravel 11 wraps AuthorizationException
            // into an HttpException before reaching this handler).
            $prev = $e->getPrevious();

            [$code, $message, $status, $fields] = match (true) {
                $e instanceof \Illuminate\Validation\ValidationException
                    => ['VALIDATION_FAILED', $e->getMessage(), 422, $e->errors()],
                $e instanceof \Illuminate\Auth\AuthenticationException
                    => ['UNAUTHENTICATED', 'Authentication required.', 401, []],
                $e instanceof \Illuminate\Auth\Access\AuthorizationException,
                $prev instanceof \Illuminate\Auth\Access\AuthorizationException
                    => ['FORBIDDEN', $e->getMessage() ?: 'Forbidden.', 403, []],
                $e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
                    => ['FORBIDDEN', $e->getMessage() ?: 'Forbidden.', 403, []],
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
                $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                    => ['NOT_FOUND', 'Resource not found.', 404, []],
                $e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
                    => ['METHOD_NOT_ALLOWED', 'HTTP method not allowed.', 405, []],
                $e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException
                    => ['RATE_LIMITED', 'Too many requests.', 429, []],
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                    => ['HTTP_ERROR', $e->getMessage() ?: 'HTTP error.', $e->getStatusCode(), []],
                default
                    => ['SERVER_ERROR', config('app.debug') ? $e->getMessage() : 'Server error.', 500, []],
            };
            $body = ['error' => ['code' => $code, 'message' => $message]];
            if ($fields) $body['error']['fields'] = $fields;
            return response()->json($body, $status);
        });
    })->create();
