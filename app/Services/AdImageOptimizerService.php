<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Async optimizer for ad images.
 *
 * - Keeps upload fast (job queue), then compresses in background.
 * - Produces two variants from the uploaded source file:
 *     products/{name}        → display image (used on detail page)
 *     products/thumb/{name}  → small thumbnail (used in listing grids)
 */
class AdImageOptimizerService
{
    private int $displayMaxWidth;

    private int $displayMaxHeight;

    private int $thumbMaxWidth;

    private int $thumbMaxHeight;

    private int $jpegQuality;

    private int $webpQuality;

    public function __construct()
    {
        $this->displayMaxWidth = max(320, (int) config('quickad.ads.display_max_width', 1600));
        $this->displayMaxHeight = max(320, (int) config('quickad.ads.display_max_height', 1600));
        $this->thumbMaxWidth = max(120, (int) config('quickad.ads.thumb_max_width', 480));
        $this->thumbMaxHeight = max(120, (int) config('quickad.ads.thumb_max_height', 480));
        $this->jpegQuality = max(50, min(92, (int) config('quickad.ads.jpeg_quality', 82)));
        $this->webpQuality = max(50, min(92, (int) config('quickad.ads.webp_quality', 80)));
    }

    public function optimize(string $filename): void
    {
        $filename = basename($filename);
        $disk = Storage::disk('public');
        $displayRel = 'products/'.$filename;
        $thumbRel = 'products/thumb/'.$filename;

        if (!$disk->exists($displayRel)) {
            return;
        }

        $displayPath = $disk->path($displayRel);
        $thumbPath = $disk->path($thumbRel);

        if (!is_dir(dirname($thumbPath))) {
            @mkdir(dirname($thumbPath), 0775, true);
        }

        try {
            if (extension_loaded('imagick')) {
                $this->optimizeWithImagick($displayPath, $thumbPath);

                return;
            }

            if (extension_loaded('gd')) {
                $this->optimizeWithGd($displayPath, $thumbPath);

                return;
            }

            Log::warning('Ad image optimization skipped (no imagick/gd loaded)', ['file' => $filename]);
        } catch (\Throwable $e) {
            Log::warning('Ad image optimization failed', [
                'file' => $filename,
                'error_type' => $e::class,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function optimizeWithImagick(string $displayPath, string $thumbPath): void
    {
        $img = new \Imagick($displayPath);

        if (method_exists($img, 'autoOrient')) {
            $img->autoOrient();
        }

        $this->resizeImagickWithin($img, $this->displayMaxWidth, $this->displayMaxHeight);
        $img->stripImage();
        $this->applyImagickCompression($img, $displayPath);
        $img->writeImage($displayPath);

        $thumb = clone $img;
        $this->resizeImagickWithin($thumb, $this->thumbMaxWidth, $this->thumbMaxHeight);
        $thumb->stripImage();
        $this->applyImagickCompression($thumb, $thumbPath);
        $thumb->writeImage($thumbPath);

        $thumb->clear();
        $thumb->destroy();
        $img->clear();
        $img->destroy();
    }

    private function resizeImagickWithin(\Imagick $img, int $maxWidth, int $maxHeight): void
    {
        $w = $img->getImageWidth();
        $h = $img->getImageHeight();
        if ($w <= $maxWidth && $h <= $maxHeight) {
            return;
        }

        $scale = min($maxWidth / max(1, $w), $maxHeight / max(1, $h));
        $nw = max(1, (int) floor($w * $scale));
        $nh = max(1, (int) floor($h * $scale));

        $img->resizeImage($nw, $nh, \Imagick::FILTER_LANCZOS, 1, true);
    }

    private function applyImagickCompression(\Imagick $img, string $targetPath): void
    {
        $ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg'], true)) {
            $img->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $img->setImageCompressionQuality($this->jpegQuality);
            // Reduce chroma bandwidth slightly for better size.
            @$img->setSamplingFactors(['2x2', '1x1', '1x1']);
        } elseif ($ext === 'webp') {
            $img->setImageFormat('webp');
            $img->setImageCompressionQuality($this->webpQuality);
        } elseif ($ext === 'png') {
            // PNG quality in ImageMagick maps to zlib effort.
            $img->setImageCompressionQuality(85);
        }
    }

    private function optimizeWithGd(string $displayPath, string $thumbPath): void
    {
        $info = @getimagesize($displayPath);
        if (!$info || empty($info['mime'])) {
            return;
        }

        $src = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($displayPath),
            'image/png' => @imagecreatefrompng($displayPath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($displayPath) : false,
            default => false,
        };
        if (!$src) {
            return;
        }

        $src = $this->autoOrientGd($src, $displayPath, $info['mime']);

        $display = $this->gdResizeWithin($src, $this->displayMaxWidth, $this->displayMaxHeight);
        $this->gdSave($display, $displayPath, $info['mime']);

        $thumb = $this->gdResizeWithin($display, $this->thumbMaxWidth, $this->thumbMaxHeight);
        $this->gdSave($thumb, $thumbPath, $info['mime']);

        imagedestroy($thumb);
        imagedestroy($display);
        imagedestroy($src);
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

    private function gdResizeWithin(\GdImage $src, int $maxWidth, int $maxHeight): \GdImage
    {
        $w = imagesx($src);
        $h = imagesy($src);
        if ($w <= $maxWidth && $h <= $maxHeight) {
            $dst = imagecreatetruecolor($w, $h);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);

            return $dst;
        }

        $scale = min($maxWidth / max(1, $w), $maxHeight / max(1, $h));
        $nw = max(1, (int) floor($w * $scale));
        $nh = max(1, (int) floor($h * $scale));

        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        return $dst;
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
