<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Builds a clean, buyer-ready ZIP of the full product (Laravel backend +
 * Next.js frontend) for source-code delivery.
 *
 *   php artisan release:build --version=1.4.0
 *
 * The output is a single archive under storage/app/releases/ that contains
 * ONLY distributable source — no secrets (.env), no VCS (.git), no build
 * artefacts (node_modules, vendor, .next), no logs. Package docs (README,
 * INSTALLATION, CHANGELOG, LICENSE) are generated from stubs in
 * resources/release/.
 *
 * The archive is deterministic and idempotent: re-running for the same
 * version rebuilds it from scratch.
 */
class BuildReleasePackage extends Command
{
    // NOTE: uses --tag (not --version): Symfony Console reserves --version/-V
    // for printing the app version, which would shadow our option.
    protected $signature = 'release:build
        {--tag= : Semantic version for the package, e.g. 1.4.0}
        {--name=esawda-marketplace : Base name for the archive}';

    protected $description = 'Build a clean, distributable ZIP of the backend + frontend source';

    /**
     * Paths (relative to each app root) that must NEVER ship. Matched against
     * any path segment, so "vendor" excludes vendor/ at any depth.
     */
    private array $excludedSegments = [
        '.git', '.github', '.gitignore', '.gitattributes',
        'node_modules', 'vendor', '.next', '.turbo', '.idea', '.vscode',
        'storage/logs', 'storage/framework', 'storage/app/releases',
        'storage/app/release-build', 'storage/app/public', 'public/storage',
        'bootstrap/cache',
        '.env', '.env.local', '.env.production', '.env.testing',
        '.DS_Store', 'Thumbs.db',
        'reviews', '.claude', '.rovodev', '.playwright-mcp',
        // Infra / deploy secrets and scratch material that must not ship.
        'deploy', '.github', 'DEPLOY.md', 'esawda_dns_records.txt',
        '.phpunit.result.cache',
    ];

    /** Filename patterns that must never ship (secrets / scratch files). */
    private array $excludedPatterns = [
        '*.bak', '*.log', '*.sqlite', '*.pem', '*.key',
        'tmp_rovodev_*', '*.original.md',
        'dgepay_demo*.php', '*.postman_collection*.json',
    ];

    /**
     * Extra files to drop, but ONLY at the backend root (loose scratch assets
     * left in the repo root — screenshots, vendor PDFs). We don't blanket-ban
     * images because frontend/public legitimately contains them.
     */
    private array $excludedRootGlobs = [
        '*.png', '*.jpg', '*.jpeg', '*.gif', '*.pdf', '*.txt',
    ];

    public function handle(): int
    {
        $version = (string) ($this->option('tag') ?: '');
        if (!preg_match('/^\d+\.\d+\.\d+([-.\w]*)?$/', $version)) {
            $this->error('Please pass a valid --tag, e.g. --tag=1.4.0');

            return self::FAILURE;
        }

        $base = Str::slug((string) $this->option('name')) ?: 'esawda-marketplace';
        $root = base_path();
        $frontend = base_path('frontend');

        if (!is_dir($frontend)) {
            $this->error("Frontend directory not found at: {$frontend}");

            return self::FAILURE;
        }

        // Stage OUTSIDE the project tree so the recursive copy can never pick
        // up its own output (which would loop forever).
        $work = rtrim(sys_get_temp_dir(), '/').'/esawda-release-'.Str::random(8);
        $stage = $work.'/'.$base.'-v'.$version;
        $outDir = storage_path('app/releases');
        $zipPath = $outDir.'/'.$base.'-v'.$version.'.zip';

        File::ensureDirectoryExists($stage.'/backend');
        File::ensureDirectoryExists($stage.'/frontend');
        File::ensureDirectoryExists($stage.'/docs');
        File::ensureDirectoryExists($outDir);

        $this->info("Building {$base} v{$version} …");

        // 1. Copy backend (repo root, excluding the frontend/ subdir).
        $this->line('  • Copying backend source');
        $this->copyTree($root, $stage.'/backend', skipTopLevel: ['frontend']);

        // 2. Copy frontend.
        $this->line('  • Copying frontend source');
        $this->copyTree($frontend, $stage.'/frontend');

        // 3. Docs + generated package files.
        $this->line('  • Writing package docs');
        $this->writeDocs($stage, $version);

        // 4. Zip it.
        $this->line('  • Compressing archive');
        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }
        $count = $this->zipDir($stage, $zipPath, $base.'-v'.$version);

        // 5. Cleanup temp.
        File::deleteDirectory($work);

        $sizeMb = round(filesize($zipPath) / 1048576, 2);
        $sha = hash_file('sha256', $zipPath);

        $this->newLine();
        $this->info('✔ Release built');
        $this->table(['Field', 'Value'], [
            ['File', $zipPath],
            ['Files', $count],
            ['Size', $sizeMb.' MB'],
            ['SHA-256', $sha],
        ]);

        return self::SUCCESS;
    }

    /**
     * Recursively copy $src into $dst, applying exclusion rules. Optionally
     * skip named top-level entries (used to keep frontend/ out of backend).
     */
    private function copyTree(string $src, string $dst, array $skipTopLevel = [], ?string $appRoot = null): void
    {
        // Exclusions are evaluated relative to the app root passed on the FIRST
        // call, so multi-segment rules like "storage/framework" keep matching
        // as we recurse deeper.
        $appRoot ??= $src;
        File::ensureDirectoryExists($dst);

        foreach (scandir($src) ?: [] as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            $dir = $src.'/'.$name;
            // Skip anything that isn't a real, readable directory (this also
            // sidesteps broken symlinks like public/storage/*).
            if (!is_dir($dir) || is_link($dir)) {
                continue;
            }
            if (in_array($name, $skipTopLevel, true) || $this->isExcluded($dir, $appRoot)) {
                continue;
            }
            $this->copyTree($dir, $dst.'/'.$name, [], $appRoot);
        }

        foreach (scandir($src) ?: [] as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            $path = $src.'/'.$name;
            // Only real, readable files — skip dirs, symlinks and dangling links.
            if (!is_file($path) || is_link($path) || !is_readable($path)) {
                continue;
            }
            if ($this->isExcluded($path, $appRoot)) {
                continue;
            }
            File::copy($path, $dst.'/'.$name);
        }
    }

    /** True if the given path should be excluded from the package. */
    private function isExcluded(string $path, string $appRoot): bool
    {
        $rel = ltrim(str_replace('\\', '/', Str::after($path, $appRoot)), '/');
        $name = basename($path);

        foreach ($this->excludedSegments as $seg) {
            if ($rel === $seg || Str::startsWith($rel.'/', $seg.'/') || Str::contains('/'.$rel.'/', '/'.$seg.'/')) {
                return true;
            }
        }
        foreach ($this->excludedPatterns as $pat) {
            if (fnmatch($pat, $name)) {
                return true;
            }
        }

        // Root-level scratch assets only (no slash in the relative path).
        if (!Str::contains($rel, '/')) {
            foreach ($this->excludedRootGlobs as $glob) {
                if (fnmatch($glob, $name)) {
                    return true;
                }
            }
        }

        return false;
    }

    /** Render doc stubs and drop in the postman collection. */
    private function writeDocs(string $stage, string $version): void
    {
        $replacements = [
            '{{VERSION}}' => $version,
            '{{DATE}}' => now()->toFormattedDateString(),
            '{{YEAR}}' => now()->format('Y'),
        ];

        $map = [
            'README.stub' => $stage.'/README.md',
            'INSTALLATION.stub' => $stage.'/INSTALLATION.md',
            'CHANGELOG.stub' => $stage.'/CHANGELOG.md',
            'LICENSE.stub' => $stage.'/LICENSE.txt',
        ];

        foreach ($map as $stub => $target) {
            $stubPath = resource_path('release/'.$stub);
            $content = File::exists($stubPath) ? File::get($stubPath) : '';
            File::put($target, strtr($content, $replacements));
        }

        // Bundle the API collection under docs/ if present.
        $postman = base_path('docs/offersale.postman_collection.json');
        if (File::exists($postman)) {
            File::copy($postman, $stage.'/docs/api-postman-collection.json');
        }

        // A short deployment pointer so docs/ is never empty.
        File::put(
            $stage.'/docs/deployment-guide.md',
            "# Deployment Guide\n\nSee INSTALLATION.md for setup. For production, serve\n".
            "the backend `public/` via Nginx, run the frontend with PM2/systemd,\n".
            "start a queue worker (`php artisan queue:work`) and add the scheduler\n".
            "cron (`* * * * * php artisan schedule:run`).\n"
        );
    }

    /** Zip an entire directory tree under a top-level $prefix folder. */
    private function zipDir(string $dir, string $zipPath, string $prefix): int
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip at {$zipPath}");
        }

        $count = 0;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($it as $item) {
            /** @var \SplFileInfo $item */
            $local = $prefix.'/'.ltrim(str_replace('\\', '/', Str::after($item->getPathname(), $dir)), '/');
            if ($item->isDir()) {
                $zip->addEmptyDir($local);
            } else {
                $zip->addFile($item->getPathname(), $local);
                $count++;
            }
        }

        $zip->close();

        return $count;
    }
}
