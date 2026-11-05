<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Mandatory downscale for profile-style uploads (avatar / cover / shop banner).
 *
 * The frontend's Next image optimizer handles delivery sizes, but keeping
 * 13k x 13k source files on disk makes the first optimization of every cache
 * cold start extremely slow. These uploads are stored for display at most a
 * couple of thousand pixels wide, so resizing at upload time is safe and keeps
 * the source pool small.
 *
 * Optimization is best-effort: a failure is logged and the original file is
 * kept, so an upload never fails because of the optimizer.
 */
class ProfileImageOptimizerService
{
    private int $jpegQuality = 85;

    private int $webpQuality = 82;

    public function optimize(string $relativePath, int $maxWidth, int $maxHeight): void
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($relativePath)) {
            return;
        }

        $path = $disk->path($relativePath);

        try {
            if (extension_loaded('imagick')) {
                $this->optimizeWithImagick($path, $maxWidth, $maxHeight);

                return;
            }

            if (extension_loaded('gd')) {
                $this->optimizeWithGd($path, $maxWidth, $maxHeight);

                return;
            }

            Log::warning('Profile image optimization skipped (no imagick/gd loaded)', [
                'file' => $relativePath,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Profile image optimization failed', [
                'file' => $relativePath,
                'error_type' => $e::class,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function optimizeWithImagick(string $path, int $maxWidth, int $maxHeight): void
    {
        $img = new \Imagick($path);

        try {
            if (method_exists($img, 'autoOrient')) {
                $img->autoOrient();
            }

            $width = $img->getImageWidth();
            $height = $img->getImageHeight();

            if ($width > $maxWidth || $height > $maxHeight) {
                $scale = min($maxWidth / max(1, $width), $maxHeight / max(1, $height));
                $newWidth = max(1, (int) floor($width * $scale));
                $newHeight = max(1, (int) floor($height * $scale));

                $img->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1, true);
            }

            $img->stripImage();

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg'], true)) {
                $img->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $img->setImageCompressionQuality($this->jpegQuality);
                @$img->setSamplingFactors(['2x2', '1x1', '1x1']);
            } elseif ($extension === 'webp') {
                $img->setImageFormat('webp');
                $img->setImageCompressionQuality($this->webpQuality);
            } elseif ($extension === 'png') {
                // PNG quality maps to zlib effort in ImageMagick.
                $img->setImageCompressionQuality(85);
            }

            $img->writeImage($path);
        } finally {
            $img->clear();
            $img->destroy();
        }
    }

    private function optimizeWithGd(string $path, int $maxWidth, int $maxHeight): void
    {
        $info = @getimagesize($path);
        if (!$info || empty($info['mime'])) {
            return;
        }

        $source = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (!$source) {
            return;
        }

        try {
            $source = $this->autoOrientGd($source, $path, $info['mime']);
            $width = imagesx($source);
            $height = imagesy($source);

            if ($width > $maxWidth || $height > $maxHeight) {
                $scale = min($maxWidth / max(1, $width), $maxHeight / max(1, $height));
                $newWidth = max(1, (int) floor($width * $scale));
                $newHeight = max(1, (int) floor($height * $scale));

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                imagedestroy($source);
                $source = $resized;
            }

            $this->gdSave($source, $path, $info['mime']);
        } finally {
            if ($source instanceof \GdImage) {
                imagedestroy($source);
            }
        }
    }

    private function autoOrientGd(\GdImage $img, string $path, string $mime): \GdImage
    {
        if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
            return $img;
        }

        $exif = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        return match ($orientation) {
            3 => imagerotate($img, 180, 0) ?: $img,
            6 => imagerotate($img, -90, 0) ?: $img,
            8 => imagerotate($img, 90, 0) ?: $img,
            default => $img,
        };
    }

    private function gdSave(\GdImage $img, string $path, string $mime): void
    {
        match ($mime) {
            'image/jpeg' => imagejpeg($img, $path, $this->jpegQuality),
            'image/png' => imagepng($img, $path, 6),
            'image/webp' => function_exists('imagewebp') ? imagewebp($img, $path, $this->webpQuality) : imagejpeg($img, $path, $this->jpegQuality),
            default => imagejpeg($img, $path, $this->jpegQuality),
        };
    }
}
