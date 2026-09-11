<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Utilities for legacy `product.screen_shot` image names + variant URLs.
 *
 * We keep storing bare filenames in DB, then derive:
 *   - display: /storage/products/{name}
 *   - thumb:   /storage/products/thumb/{name}
 */
class AdImage
{
    public static function namesFromRaw(mixed $raw): array
    {
        if (empty($raw) || $raw === '[]' || $raw === 'null') {
            return [];
        }

        if (is_array($raw)) {
            $names = $raw;
        } else {
            $decoded = json_decode((string) $raw, true);
            $names = is_array($decoded)
                ? $decoded
                : (preg_split('/[,;\s]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY) ?: []);
        }

        return array_values(array_filter(
            array_map(fn ($n) => trim((string) $n), $names),
            fn ($n) => $n !== '' && $n !== '[]' && $n !== '{}'
        ));
    }

    public static function displayPath(string $name): string
    {
        return 'products/'.ltrim($name, '/');
    }

    public static function thumbPath(string $name): string
    {
        return 'products/thumb/'.ltrim($name, '/');
    }

    public static function displayUrl(string $name): string
    {
        // Full URL in DB is allowed and remains untouched.
        if (preg_match('~^https?://~i', $name)) {
            return $name;
        }

        $name = ltrim($name, '/');
        $base = rtrim((string) config('app.url'), '/').'/storage/';

        return $base.self::displayPath($name);
    }

    public static function thumbUrl(string $name): string
    {
        if (preg_match('~^https?://~i', $name)) {
            return $name;
        }

        $name = ltrim($name, '/');
        $disk = Storage::disk('public');

        if ($disk->exists(self::thumbPath($name))) {
            return rtrim((string) config('app.url'), '/').'/storage/'.self::thumbPath($name);
        }

        return self::displayUrl($name);
    }

    public static function deleteAllVariants(string $name): void
    {
        if (preg_match('~^https?://~i', $name)) {
            return;
        }

        $name = ltrim($name, '/');
        Storage::disk('public')->delete([
            self::displayPath($name),
            self::thumbPath($name),
        ]);
    }
}
