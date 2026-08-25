<?php

namespace App\Services;

/**
 * Port of `plugins/watermark/watermark.php`. Applies a bottom-right
 * watermark PNG onto an uploaded product image. Uses GD directly to
 * stay dependency-free; swap for Intervention Image if preferred.
 */
class WatermarkService
{
    public function apply(string $targetPath, string $watermarkPath, string $outputPath): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        $wm = imagecreatefrompng($watermarkPath);
        imagealphablending($wm, false);
        imagesavealpha($wm, true);

        $info = getimagesize($targetPath);
        [$imgW, $imgH] = [$info[0], $info[1]];

        $img = match ($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($targetPath),
            'image/png' => imagecreatefrompng($targetPath),
            default => null,
        };
        if (!$img) {
            return false;
        }

        $wmW = imagesx($wm);
        $wmH = imagesy($wm);
        // Bottom-right corner placement (legacy behaviour).
        imagecopy($img, $wm, $imgW - $wmW, $imgH - $wmH, 0, 0, $wmW, $wmH);

        $ok = match ($info['mime']) {
            'image/jpeg' => imagejpeg($img, $outputPath, 100),
            'image/png' => imagepng($img, $outputPath, 5),
        };
        imagedestroy($img);
        imagedestroy($wm);

        return (bool) $ok;
    }
}
