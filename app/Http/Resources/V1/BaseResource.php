<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Base for every v1 resource.
 *
 * Provides small type-coercion helpers so we can hide the legacy DB
 * quirks (enum '0'/'1' as strings, unix ints, JSON blobs in text columns)
 * at the API boundary instead of leaking them to clients.
 */
abstract class BaseResource extends JsonResource
{
    protected function bool($value): bool
    {
        return in_array($value, [1, '1', true, 'true', 'yes', 'on'], true);
    }

    protected function unixToIso(?int $ts): ?string
    {
        if (! $ts) return null;
        return date(DATE_ATOM, (int) $ts);
    }

    /** Legacy `screen_shot` column may be a JSON array, comma list, or single filename. */
    protected function images($raw): array
    {
        if (empty($raw) || $raw === '[]' || $raw === 'null') return [];

        if (is_array($raw)) {
            $names = $raw;
        } else {
            $decoded = json_decode((string) $raw, true);
            $names   = is_array($decoded)
                ? $decoded
                : (preg_split('/[,;\s]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY) ?: []);
        }

        // Discard falsy / punctuation-only entries (guards against ""[]"" etc.).
        $names = array_values(array_filter(
            $names,
            fn ($n) => is_string($n) && trim($n) !== '' && trim($n) !== '[]' && trim($n) !== '{}'
        ));
        if (! $names) return [];

        $base = rtrim(config('app.url'), '/') . '/storage/products/';
        return array_map(function ($n) use ($base) {
            $n = (string) $n;
            // Allow storing either a bare filename (legacy) or a full URL
            // (preferred going forward — avoids leaky storage layout details).
            $url = preg_match('~^https?://~i', $n) ? $n : $base . ltrim($n, '/');
            return ['url' => $url, 'thumb' => $url];
        }, $names);
    }

    /** "12.34,56.78" → ['lat'=>12.34, 'lng'=>56.78] or null. */
    protected function latLng(?string $raw): ?array
    {
        if (! $raw || ! str_contains($raw, ',')) return null;
        [$lat, $lng] = array_map('trim', explode(',', $raw, 2));
        return is_numeric($lat) && is_numeric($lng)
            ? ['lat' => (float) $lat, 'lng' => (float) $lng]
            : null;
    }
}
