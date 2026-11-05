<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ProfileImageOptimizerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * One-off / repeatable backfill for profile uploads that predate upload-time
 * resizing. It only touches files larger than their role's display limit.
 *
 * Dry run first:
 *   php artisan images:optimize-profile --dry-run
 *
 * Apply with originals kept:
 *   php artisan images:optimize-profile --backup
 */
class OptimizeProfileImages extends Command
{
    protected $signature = 'images:optimize-profile
        {--dry-run : Report what would change without writing}
        {--backup : Copy originals to profile-originals/ before resizing}';

    protected $description = 'Downscale oversized avatar / cover / shop-banner files in storage/app/public/profile';

    public function handle(ProfileImageOptimizerService $optimizer): int
    {
        $disk = Storage::disk('public');
        $limits = $this->limitsByFilename();

        $files = $disk->files('profile');
        $before = 0;
        $after = 0;
        $changed = 0;
        $skipped = 0;

        foreach ($files as $relative) {
            $name = basename($relative);
            $path = $disk->path($relative);

            if (!is_file($path)) {
                continue;
            }

            [$maxWidth, $maxHeight] = $limits[$name] ?? [2000, 2000];
            $info = @getimagesize($path);
            $sizeBefore = (int) filesize($path);
            $before += $sizeBefore;

            if (!$info) {
                $skipped++;
                $after += $sizeBefore;

                continue;
            }

            [$width, $height] = $info;
            $needsResize = $width > $maxWidth || $height > $maxHeight;

            if (!$needsResize) {
                $after += $sizeBefore;

                continue;
            }

            $changed++;
            $this->line(sprintf(
                '%s  %dx%d  %.2f MB → max %dx%d',
                $name,
                $width,
                $height,
                $sizeBefore / 1048576,
                $maxWidth,
                $maxHeight,
            ));

            if ($this->option('dry-run')) {
                $after += $sizeBefore;

                continue;
            }

            if ($this->option('backup')) {
                $backupDir = storage_path('app/private/backups/profile-originals');
                if (!is_dir($backupDir)) {
                    @mkdir($backupDir, 0775, true);
                }

                $backupPath = $backupDir.'/'.$name;
                if (!is_file($backupPath)) {
                    copy($path, $backupPath);
                }
            }

            $optimizer->optimize($relative, $maxWidth, $maxHeight);
            clearstatcache(true, $path);
            $sizeAfter = (int) filesize($path);
            $after += $sizeAfter;

            $this->line(sprintf('  → %.2f MB', $sizeAfter / 1048576));
        }

        $this->newLine();
        $this->info(sprintf(
            '%s: %d file(s), %d changed, %d skipped; %.2f MB → %.2f MB',
            $this->option('dry-run') ? 'Dry run' : 'Done',
            count($files),
            $changed,
            $skipped,
            $before / 1048576,
            $after / 1048576,
        ));

        return self::SUCCESS;
    }

    /**
     * Map basename => [maxWidth, maxHeight] using the DB columns that hold it.
     * Shop banner wins over cover when a file is reused in both places.
     *
     * @return array<string, array{0:int,1:int}>
     */
    private function limitsByFilename(): array
    {
        $limits = [];

        User::query()
            ->select(['id', 'image', 'cover', 'shop_banner'])
            ->chunkById(500, function ($users) use (&$limits): void {
                foreach ($users as $user) {
                    if ($user->image) {
                        $limits[basename((string) $user->image)] = [512, 512];
                    }
                    if ($user->cover) {
                        $limits[basename((string) $user->cover)] = [2000, 1000];
                    }
                    if ($user->shop_banner) {
                        $limits[basename((string) $user->shop_banner)] = [6000, 2000];
                    }
                }
            });

        return $limits;
    }
}
