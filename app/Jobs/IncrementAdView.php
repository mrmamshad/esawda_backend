<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Fire-and-forget ad view counter.
 *
 * A synchronous `increment('view')` inside AdController@show puts an UPDATE
 * row-lock on the ad in the critical response path — under spike traffic
 * every reader of a hot ad queues behind its own view write. Dispatched to
 * the queue (redis in prod) the response returns immediately and increments
 * serialize in the worker instead.
 */
class IncrementAdView implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $adId) {}

    public function handle(): void
    {
        Post::where('id', $this->adId)->increment('view');
    }
}
