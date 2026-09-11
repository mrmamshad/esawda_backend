<?php

namespace App\Console\Commands;

use App\Jobs\OptimizeAdImageJob;
use App\Models\Post;
use App\Support\AdImage;
use Illuminate\Console\Command;

class OptimizeAdImages extends Command
{
    protected $signature = 'ads:optimize-images
        {--id= : Only optimize images for one ad id}
        {--chunk=200 : Rows per DB chunk}
        {--sync : Run inline (no queue) for maintenance windows}';

    protected $description = 'Queue optimization jobs for stored ad images (display + thumb variants).';

    public function handle(): int
    {
        $chunk = max(20, min(1000, (int) $this->option('chunk')));
        $id = $this->option('id');
        $sync = (bool) $this->option('sync');

        $q = Post::query()->select(['id', 'screen_shot'])->whereNotNull('screen_shot');
        if ($id !== null && $id !== '') {
            $q->where('id', (int) $id);
        }

        $jobs = 0;
        $rows = 0;

        $q->orderBy('id')->chunk($chunk, function ($posts) use (&$jobs, &$rows, $sync) {
            foreach ($posts as $post) {
                $rows++;
                foreach (AdImage::namesFromRaw($post->screen_shot) as $name) {
                    if ($sync) {
                        OptimizeAdImageJob::dispatchSync($name);
                    } else {
                        OptimizeAdImageJob::dispatch($name);
                    }
                    $jobs++;
                }
            }
        });

        $mode = $sync ? 'sync' : 'queued';
        $this->info("Scanned {$rows} ad(s), {$mode} {$jobs} image job(s).");

        return self::SUCCESS;
    }
}
