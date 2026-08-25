<?php

namespace App\Jobs;

use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Mail\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/** Applies successful payment side-effects exactly once. */
class FulfilTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 180];

    public function __construct(public int $transactionId) {}

    public function handle(): void
    {
        /** @var Transaction|null $tx */
        $tx = null;

        DB::transaction(function () use (&$tx): void {
            /** @var Transaction|null $tx */
            $tx = Transaction::query()->lockForUpdate()->find($this->transactionId);

            if (!$tx || $tx->status !== TransactionStatus::Success || $tx->fulfilled_at) {
                return;
            }

            switch ((string) $tx->purpose) {
                case 'plan':
                    $this->fulfilPlan($tx);
                    break;
                case 'ad_upgrade':
                    $this->fulfilAdUpgrade($tx);
                    break;
                case 'ad_post':
                    $this->fulfilAdPost($tx);
                    break;
                case 'paid_listing':
                    $this->fulfilPaidListing($tx);
                    break;
                case 'product_purchase':
                    $this->fulfilProductPurchase($tx);
                    break;
                default:
                    throw new RuntimeException("Unsupported transaction purpose: {$tx->purpose}");
            }

            $tx->forceFill(['fulfilled_at' => now(), 'updated_at' => now()])->save();
        }, 3);

        if ($tx) {
            $this->dispatchEmails($tx);
        }
    }

    /**
     * Fire the transactional emails for a successfully fulfilled
     * transaction. Runs after the DB transaction commits so the queue job
     * can safely serialise the fresh models.
     */
    private function dispatchEmails(Transaction $tx): void
    {
        $mail = app(MailService::class);

        try {
            switch ((string) $tx->purpose) {
                case 'product_purchase':
                    $order = Order::where('transaction_id', $tx->id)->first();
                    if ($order) {
                        $mail->newOrderToSeller($order);
                        $mail->paymentSuccessToBuyer($order);
                    }
                    $mail->transactionToAdmin($tx, $order);
                    break;

                case 'plan':
                    $mail->planActivatedToUser(User::find($tx->seller_id));
                    $mail->transactionToAdmin($tx);
                    break;

                case 'ad_post':
                case 'paid_listing':
                    $post = Post::find($tx->product_id);
                    if ($post) {
                        $mail->pendingAdToAdmin($post->load('user'));
                    }
                    $mail->transactionToAdmin($tx);
                    break;

                case 'ad_upgrade':
                    $mail->transactionToAdmin($tx);
                    break;

                default:
                    $mail->transactionToAdmin($tx);
                    break;
            }
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch fulfilment emails', [
                'transaction_id' => $tx->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function fulfilPlan(Transaction $tx): void
    {
        $plan = Plan::find($tx->plan_id);
        $user = User::find($tx->seller_id);

        if (!$plan || !$user) {
            throw new RuntimeException("Missing plan or user for transaction {$tx->id}");
        }

        $meta = $this->meta($tx);
        $this->activatePlan($user, $plan, ($meta['cadence'] ?? 'monthly') === 'annual' ? 'annual' : 'monthly');
    }

    private function activatePlan(User $user, Plan $plan, string $cadence): void
    {
        $settings = is_array($plan->settings)
            ? $plan->settings
            : (json_decode((string) $plan->settings, true) ?: []);
        $base = $user->plan_expires_at?->isFuture()
            ? $user->plan_expires_at->copy()
            : now();
        $expiresAt = $cadence === 'annual' ? $base->addYear() : $base->addMonth();

        $user->forceFill([
            'group_id' => $plan->name ?? $user->group_id,
            'plan_id' => $plan->id,
            'plan_expires_at' => $expiresAt,
            'ads_remaining' => (int) ($settings['ads_limit'] ?? 10),
            'updated_at' => now(),
        ])->save();
    }

    private function fulfilAdUpgrade(Transaction $tx): void
    {
        $post = Post::find($tx->product_id);
        if (!$post) {
            throw new RuntimeException("Missing product for upgrade transaction {$tx->id}");
        }

        $flags = $this->meta($tx);
        $post->forceFill([
            'featured' => !empty($flags['featured']) ? '1' : $post->featured,
            'urgent' => !empty($flags['urgent']) ? '1' : $post->urgent,
            'highlight' => !empty($flags['highlight']) ? '1' : $post->highlight,
            'paid' => true,
            'transaction_id' => $tx->id,
            'updated_at' => now(),
        ])->save();
    }

    private function fulfilAdPost(Transaction $tx): void
    {
        $data = $this->meta($tx);
        $user = User::find($tx->seller_id);
        if (!$user) {
            throw new RuntimeException("Missing user for ad-post transaction {$tx->id}");
        }

        $post = Post::create([
            'user_id' => $user->id,
            'product_name' => $data['title'] ?? 'Untitled',
            'description' => $data['description'] ?? '',
            'price' => (int) ($data['price'] ?? 0),
            'category' => (int) ($data['category'] ?? 0),
            'sub_category' => isset($data['sub_category']) ? (int) $data['sub_category'] : null,
            'condition' => $data['condition'] ?? 'used',
            'phone' => $data['phone'] ?? null,
            'location' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'status' => 'pending',
            'paid' => true,
            'transaction_id' => $tx->id,
            'featured' => !empty($data['featured']) ? '1' : '0',
            'urgent' => !empty($data['urgent']) ? '1' : '0',
            'highlight' => !empty($data['highlight']) ? '1' : '0',
            'hide' => '0',
            'expire_date' => now()->addDays(60)->timestamp,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tx->forceFill(['product_id' => $post->id])->save();

        if ($plan = Plan::find($tx->plan_id)) {
            $this->activatePlan($user, $plan, 'monthly');
        } elseif ($tx->plan_id) {
            Log::warning('Plan missing for paid ad-post transaction', ['transaction_id' => $tx->id, 'plan_id' => $tx->plan_id]);
        }
    }

    private function fulfilPaidListing(Transaction $tx): void
    {
        $post = Post::find($tx->product_id);
        if (!$post) {
            throw new RuntimeException("Missing product for paid-listing transaction {$tx->id}");
        }

        $post->forceFill([
            'paid' => true,
            'status' => 'pending',
            'hide' => '0',
            'transaction_id' => $tx->id,
            'updated_at' => now(),
        ])->save();
    }

    private function fulfilProductPurchase(Transaction $tx): void
    {
        $post = Post::find($tx->product_id);
        if (!$post) {
            throw new RuntimeException("Missing product for purchase transaction {$tx->id}");
        }

        $meta = $this->meta($tx);
        $order = Order::firstOrCreate(
            ['transaction_id' => $tx->id],
            [
                'product_id' => $post->id,
                'buyer_id' => $meta['buyer_id'] ?? $tx->seller_id,
                'seller_id' => $meta['seller_id'] ?? $post->user_id,
                'amount' => $tx->amount,
                'shipping_status' => 'pending',
                'seller_paid' => false,
            ],
        );

        if ($order->shipping_status === 'pending') {
            $order->forceFill(['shipping_status' => 'processing'])->save();
        }

        $post->forceFill(['status' => 'sold_out', 'transaction_id' => $tx->id, 'updated_at' => now()])->save();
    }

    private function meta(Transaction $tx): array
    {
        return json_decode((string) ($tx->meta ?? '{}'), true) ?: [];
    }
}
