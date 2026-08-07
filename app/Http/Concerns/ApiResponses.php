<?php

namespace App\Http\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Uniform response envelope: { data, meta, links? } for success and
 * { error: { code, message, fields? } } for failures.
 *
 * All controllers extend App\Http\Controllers\Controller which imports
 * this trait, so calling $this->ok(...) / $this->created(...) / etc. is
 * available everywhere.
 */
trait ApiResponses
{
    protected function ok($data = null, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json($this->envelope($data, $meta), $status);
    }

    protected function created($data = null, array $meta = []): JsonResponse
    {
        return $this->ok($data, $meta, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Attach the Sanctum token as an HttpOnly + Secure cookie on the
     * response so the Next.js server can forward it (apiFromServer) while
     * browser JS can't read it. `Secure` is only set when the request was
     * served over HTTPS so local HTTP dev still works.
     */
    protected function withAuthCookie(JsonResponse $response, string $token): JsonResponse
    {
        $request  = request();
        $isSecure = $request->isSecure() || $request->headers->get('X-Forwarded-Proto') === 'https';

        return $response->withCookie(cookie(
            'eshauda_token',
            $token,
            60 * 24 * 30,              // 30 days
            '/',
            null,
            $isSecure,
            true,                       // HttpOnly — not readable by JS
            false,
            'Lax',
        ));
    }

    protected function withClearedAuthCookie(JsonResponse $response): JsonResponse
    {
        return $response->withCookie(cookie(
            'eshauda_token',
            '',
            -60,
            '/',
            null,
            app()->isProduction(),
            true,
            false,
            'Lax',
        ));
    }

    protected function error(string $code, string $message, int $status = 400, array $fields = []): JsonResponse
    {
        $body = ['error' => ['code' => $code, 'message' => $message]];
        if ($fields) $body['error']['fields'] = $fields;
        return response()->json($body, $status);
    }

    /**
     * Build the { data, meta, links } envelope for any input:
     *  - JsonResource   → data = $resource->resolve()
     *  - ResourceCollection wrapping a paginator → data + meta + links
     *  - LengthAwarePaginator → same
     *  - array/object   → data as-is
     */
    protected function envelope($data, array $extraMeta = []): array
    {
        if ($data instanceof ResourceCollection) {
            $data = $data->toResponse(request())->getData(true); // already { data, meta, links }
            $data['meta'] = array_merge($data['meta'] ?? [], $extraMeta);
            return $data;
        }
        if ($data instanceof LengthAwarePaginator) {
            return [
                'data'  => $data->items(),
                'meta'  => array_merge([
                    'current_page' => $data->currentPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                    'last_page'    => $data->lastPage(),
                ], $extraMeta),
                'links' => [
                    'first' => $data->url(1),
                    'last'  => $data->url($data->lastPage()),
                    'prev'  => $data->previousPageUrl(),
                    'next'  => $data->nextPageUrl(),
                ],
            ];
        }
        if ($data instanceof JsonResource) {
            $data = $data->resolve();
        }
        $out = ['data' => $data];
        if ($extraMeta) $out['meta'] = $extraMeta;
        return $out;
    }
}
