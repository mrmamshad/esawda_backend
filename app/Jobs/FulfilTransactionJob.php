<?php

namespace App\Jobs;

use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Post-payment side-effects for a successful transaction:
 *   - plan purchase → bump user's group_id + plan_expiry
 *   - ad upgrade   → flip featured/urgent/highlight on the post
 *
 * Idempotent: safe to run repeatedly from IPN + success POST. Extracted
 * from PaymentCallbackController::fulfil so the public callback path
 * returns fast; with QUEUE_CONNECTION=sync (dev) it still runs inline.
 */
class FulfilTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $transactionId) {}

    public function handle(): void
    {
        /** @var Transaction|null $tx */
        $tx = Transaction::find($this->transactionId);
        if (! $tx || $tx->status !== 'success') return;

        DB::transaction(function () use ($tx) {
            $purpose = $tx->purpose ?? '';

            if ($purpose === 'plan' && ! empty($tx->plan_id)) {
                $plan = Plan::find($tx->plan_id);
                $user = User::find($tx->seller_id);
                if ($plan && $user) {
                    $user->forceFill([
                        'group_id'    => $plan->slug ?? $plan->name ?? $user->group_id,
                        'plan_expiry' => now()->addDays(30)->timestamp,
                        'updated_at'  => now(),
                    ])->save();
                }
            }

            if ($purpose === 'ad_upgrade' && ! empty($tx->product_id)) {
                $post  = Post::find($tx->product_id);
                $flags = json_decode((string) ($tx->meta ?? '{}'), true) ?: [];
                if ($post) {
                    $post->forceFill([
                        'featured'   => ! empty($flags['featured'])  ? '1' : $post->featured,
                        'urgent'     => ! empty($flags['urgent'])    ? '1' : $post->urgent,
                        'highlight'  => ! empty($flags['highlight']) ? '1' : $post->highlight,
                        'updated_at' => now(),
                    ])->save();
                }
            }
        });
    }
}
