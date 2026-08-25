<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

/**
 * Best-effort on-demand ISR revalidation for the Next.js frontend.
 *
 * Dispatched whenever a marketplace admin approves / rejects / features /
 * removes an ad so the freshly-updated listing appears on the home page
 * immediately instead of waiting out the 120s ISR cache timer. Delivered
 * via the sync queue driver in dev (inline) and the database driver in
 * production (deferred to a worker); a failure here never blocks the
 * admin action.
 */
class RevalidateFrontendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 5;

    public function __construct(
        public string $path = '/',
    ) {}

    public function handle(): void
    {
        $url = rtrim((string) config('quickad.frontend.url'), '/');
        $secret = (string) config('quickad.frontend.revalidate_secret');
        if (!filter_var($url, FILTER_VALIDATE_URL) || $secret === '') {
            return;
        }

        try {
            Http::timeout(3)->post("{$url}/api/revalidate", [
                'secret' => $secret,
                'path' => $this->path,
            ]);
        } catch (\Throwable) {
            // Non-fatal — the ISR timer will still refresh eventually.
        }
    }
}
