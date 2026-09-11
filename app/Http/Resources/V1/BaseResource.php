<?php

namespace App\Http\Resources\V1;

use App\Support\AdImage;
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
        if (!$ts) {
            return null;
        }

        return date(DATE_ATOM, (int) $ts);
    }

    /** Legacy `screen_shot` column may be a JSON array, comma list, or single filename. */
    protected function images($raw): array
    {
        $names = AdImage::namesFromRaw($raw);
        if (!$names) {
            return [];
        }

        return array_map(static fn (string $name) => [
            'url' => AdImage::displayUrl($name),
            'thumb' => AdImage::thumbUrl($name),
        ], $names);
    }

    /** "12.34,56.78" → ['lat'=>12.34, 'lng'=>56.78] or null. */
    protected function latLng(?string $raw): ?array
    {
        if (!$raw || !str_contains($raw, ',')) {
            return null;
        }
        [$lat, $lng] = array_map('trim', explode(',', $raw, 2));

        return is_numeric($lat) && is_numeric($lng)
            ? ['lat' => (float) $lat, 'lng' => (float) $lng]
            : null;
    }
}
