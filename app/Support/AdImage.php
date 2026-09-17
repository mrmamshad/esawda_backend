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
        $path = self::displayPath($name);

        // Prefer the optimized WebP sibling when it exists.
        if (Storage::disk('public')->exists($path.'.webp')) {
            return $base.$path.'.webp';
        }

        return $base.$path;
    }

    public static function thumbUrl(string $name): string
    {
        if (preg_match('~^https?://~i', $name)) {
            return $name;
        }

        $name = ltrim($name, '/');
        $disk = Storage::disk('public');
        $base = rtrim((string) config('app.url'), '/').'/storage/';
        $thumb = self::thumbPath($name);

        // Prefer the optimized WebP thumbnail, then the plain thumbnail,
        // then fall back to the (WebP or original) display image.
        if ($disk->exists($thumb.'.webp')) {
            return $base.$thumb.'.webp';
        }

        if ($disk->exists($thumb)) {
            return $base.$thumb;
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
            self::displayPath($name).'.webp',
            self::thumbPath($name),
            self::thumbPath($name).'.webp',
        ]);
    }
}
