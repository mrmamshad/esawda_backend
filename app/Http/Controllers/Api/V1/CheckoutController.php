<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PostStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreAdRequest;
use App\Jobs\FulfilTransactionJob;
use App\Models\Option;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use App\Services\AdMutationService;
use App\Services\Payment\PaymentManager;
use App\Services\PostingPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Buyer-initiated checkout endpoints.
 *
 *   POST /api/v1/checkout/plan/{planId}         → hosted page for a plan purchase
 *   POST /api/v1/checkout/ad-upgrade/{postId}   → hosted page for an ad boost
 *
 * All routes are authenticated (Sanctum). They persist a `transaction` row
 * so callbacks can locate it later, and return the configured primary
 * gateway's hosted page URL for the SPA to open.
 */
class CheckoutController extends Controller
{
    public function __construct(
        private readonly PaymentManager $manager,
        private readonly AdMutationService $ads,
    ) {}

    /** POST /checkout/plan/{planId} */
    public function plan(int $planId, Request $request)
    {
        $plan = Plan::findOrFail($planId);
        $user = $request->user();

        $data = $request->validate([
            'cadence' => ['nullable', 'in:monthly,annual'],
        ]);
        $cadence = $data['cadence'] ?? 'monthly';
        $amount = (float) ($cadence === 'annual'
            ? ($plan->annual_price ?? $plan->price ?? 0)
            : ($plan->monthly_price ?? $plan->price ?? 0));

        // Zero-price promos (Early Bird) activate instantly — no gateway,
        // no transaction row. Fulfilment runs inline exactly once.
        if ((int) ($plan->is_free ?? 0) === 1 || $amount <= 0) {
            FulfilTransactionJob::fulfilPlanNow($user, $plan, $cadence);

            return $this->ok([
                'transaction_id' => 0,
                'gateway_url' => '/shop?activated=early-bird',
                'free_activation' => true,
            ]);
        }

        if ($amount <= 0) {
            return $this->error('INVALID_PLAN', 'This plan has no price configured.', 422);
        }

        $gateway = $this->manager->primary();
        $paymentAttrs = $this->paymentAttributes($request, $gateway->slug(), ['cadence' => $cadence], $amount);
        $tx = Transaction::create([
            'seller_id' => $user->id,
            'product_id' => 0,
            'product_name' => 'Plan: '.($plan->name ?? "#{$plan->id}"),
            'plan_id' => $plan->id,
            'amount' => $amount,
            'transaction_gatway' => $gateway->slug(),
            'status' => 'pending',
            'purpose' => 'plan',
            'created_at' => now(),
            'updated_at' => now(),
            ...$paymentAttrs,
        ]);

        $url = $gateway->initiate($tx);
        if (!$url) {
            return $this->error('GATEWAY_INIT_FAILED', 'Could not start the payment session.', 502);
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'gateway_url' => $url,
        ]);
    }

    /** POST /checkout/ad-upgrade/{postId}  body: { featured?, urgent?, highlight? } */
    public function adUpgrade(int $postId, Request $request)
    {
        $data = $request->validate([
            'featured' => ['sometimes', 'boolean'],
            'urgent' => ['sometimes', 'boolean'],
            'highlight' => ['sometimes', 'boolean'],
            'hold_for_payment' => ['sometimes', 'boolean'],
        ]);

        $post = Post::findOrFail($postId);
        $this->authorize('update', $post);

        // Upgrade prices are site-wide settings set in the admin panel
        // (Settings → Premium upgrades). Defaults match the historical values.
        $settings = Option::pluck('option_value', 'option_name');
        $prices = [
            'featured' => (float) ($settings['upgrade_featured_price'] ?? 200),
            'urgent' => (float) ($settings['upgrade_urgent_price'] ?? 150),
            'highlight' => (float) ($settings['upgrade_highlight_price'] ?? 100),
        ];

        $amount = collect($prices)
            ->filter(fn ($price, $flag) => !empty($data[$flag]))
            ->sum();

        if ($amount <= 0) {
            return $this->error('NO_UPGRADES_SELECTED', 'Please select at least one upgrade.', 422);
        }

        $holdForPayment = (bool) ($data['hold_for_payment'] ?? false);
        unset($data['hold_for_payment']);
        if ($holdForPayment) {
            $data['held_post'] = true;
        }

        $gateway = $this->manager->primary();
        $paymentAttrs = $this->paymentAttributes($request, $gateway->slug(), $data, $amount);
        $tx = Transaction::create([
            'seller_id' => $request->user()->id,
            'product_id' => $post->id,
            'product_name' => 'Ad boost: '.($post->product_name ?? "#{$post->id}"),
            'amount' => $amount,
            'transaction_gatway' => $gateway->slug(),
            'status' => 'pending',
            'purpose' => 'ad_upgrade',
            'created_at' => now(),
            'updated_at' => now(),
            ...$paymentAttrs,
        ]);

        $url = $gateway->initiate($tx);
        if (!$url) {
            return $this->error('GATEWAY_INIT_FAILED', 'Could not start the payment session.', 502);
        }

        // New listing awaiting its first boost: hold it out of the admin
        // review queue until the boost is actually paid. It becomes a hidden
        // draft here and FulfilTransactionJob resubmits it on success.
        if ($holdForPayment && $post->status === PostStatus::Pending) {
            $post->forceFill(['status' => PostStatus::Draft, 'hide' => '1', 'updated_at' => now()])->save();
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'gateway_url' => $url,
        ]);
    }

    /**
     * POST /checkout/product-purchase/{postId}
     *
     * "Buy Now" (option B). Buyer pays the full product price via the primary gateway;
     * on success a real order is created (FulfilTransactionJob) and the
     * listing is marked sold. Option A (plain chat) never hits this endpoint.
     */
    public function productPurchase(int $postId, Request $request)
    {
        if (!config('payments.product_purchases_enabled', false)) {
            return $this->error(
                'PRODUCT_PURCHASE_DISABLED',
                'Online product purchases are not available. Please contact the seller directly.',
                410,
            );
        }

        $post = Post::query()
            ->active()
            ->where('hide', '0')
            ->findOrFail($postId);

        $buyer = $request->user();
        if ((int) $post->user_id === (int) $buyer->id) {
            return $this->error('OWN_PRODUCT', 'You cannot buy your own product.', 422);
        }

        $amount = (float) ($post->price ?? 0);
        if ($amount <= 0) {
            return $this->error('NOT_FOR_SALE', 'This product is not available for purchase.', 422);
        }

        $gateway = $this->manager->primary();
        $paymentAttrs = $this->paymentAttributes($request, $gateway->slug(), ['buyer_id' => $buyer->id, 'seller_id' => $post->user_id], $amount);
        DB::transaction(function () use ($post, $buyer, $amount, $gateway, $paymentAttrs, &$tx, &$order) {
            $tx = Transaction::create([
                'seller_id' => $buyer->id,
                'product_id' => $post->id,
                'product_name' => 'Purchase: '.($post->product_name ?? "#{$post->id}"),
                'amount' => $amount,
                'transaction_gatway' => $gateway->slug(),
                'status' => 'pending',
                'purpose' => 'product_purchase',
                'created_at' => now(),
                'updated_at' => now(),
                ...$paymentAttrs,
            ]);

            $order = Order::create([
                'product_id' => $post->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $post->user_id,
                'transaction_id' => $tx->id,
                'amount' => $amount,
                'shipping_status' => 'pending',
                'seller_paid' => false,
            ]);
        });

        $url = $gateway->initiate($tx);
        if (!$url) {
            return $this->error('GATEWAY_INIT_FAILED', 'Could not start the payment session.', 502);
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'order_id' => $order->id,
            'gateway_url' => $url,
        ]);
    }

    /** GET /checkout/transactions/{id} — poll for status after redirect back. */
    public function status(int $id, Request $request)
    {
        $tx = Transaction::where('id', $id)
            ->where('seller_id', $request->user()->id)
            ->firstOrFail();

        // While the gateway has not settled yet, ask it directly so a
        // cancelled/failed wallet payment reaches the browser without waiting
        // for the 5-minute reconciliation job. Never let a verification
        // hiccup break the poll — the row just stays pending. Rate-limit to
        // one gateway call per 10s while the browser polls every 2s.
        if ($tx->status === TransactionStatus::Pending && $tx->gateway_initiated_at !== null
            && Cache::add("checkout:poll-verify:{$tx->id}", true, 10)) {
            try {
                $verified = $this->manager->get($tx->transaction_gatway)->verify($tx);
                if ($verified && $tx->fresh()->status === TransactionStatus::Success) {
                    $this->fulfil($tx->fresh());
                }
                $tx = $tx->fresh() ?? $tx;
            } catch (\Throwable $e) {
                Log::warning('Checkout status poll verification failed', [
                    'transaction_id' => $tx->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->ok([
            'id' => $tx->id,
            'status' => $tx->status?->value ?? 'pending',
            'amount' => (float) $tx->amount,
            'gateway' => $tx->transaction_gatway,
            'purpose' => $tx->purpose ?? null,
            'plan_id' => $tx->plan_id ?? null,
            'post_id' => $tx->product_id ?? null,
            'created_at' => (string) $tx->created_at,
        ]);
    }

    /** Apply success side-effects when the poll is what first settles a payment. */
    private function fulfil(Transaction $tx): void
    {
        try {
            FulfilTransactionJob::dispatchSync($tx->id);
        } catch (\Throwable $e) {
            Log::critical('Poll-time payment fulfilment failed; queued for retry', [
                'transaction_id' => $tx->id,
                'error' => $e->getMessage(),
            ]);
            FulfilTransactionJob::dispatch($tx->id);
        }
    }

    /** Pay per listing without consuming a subscription slot. */
    public function paidListing(StoreAdRequest $request)
    {
        // A posting-blocked account cannot bypass the block by paying.
        if (PostingPolicy::isBlocked($request->user())) {
            return $this->error(
                'POSTING_BLOCKED',
                'Posting is disabled for this account. Please contact support.',
                403
            );
        }

        $settings = Option::pluck('option_value', 'option_name');
        $amount = (float) ($settings['paid_listing_price'] ?? 500);
        if ($amount <= 0) {
            return $this->error('INVALID_LISTING_PRICE', 'Paid listing price is not configured.', 422);
        }

        $gateway = $this->manager->primary();
        $paymentAttrs = $this->paymentAttributes($request, $gateway->slug(), [], $amount);

        $post = $this->ads->create(
            $request->user()->id,
            $request->validated(),
            (array) $request->file('images', []),
        );
        $post->forceFill([
            'status' => 'draft',
            'hide' => '1',
            'paid' => false,
            'updated_at' => now(),
        ])->save();

        $tx = Transaction::create([
            'seller_id' => $request->user()->id,
            'product_id' => $post->id,
            'product_name' => 'Paid listing: '.$post->product_name,
            'amount' => $amount,
            'transaction_gatway' => $gateway->slug(),
            'status' => 'pending',
            'purpose' => 'paid_listing',
            'created_at' => now(),
            'updated_at' => now(),
            ...$paymentAttrs,
        ]);

        $url = $gateway->initiate($tx);
        if (!$url) {
            $tx->forceFill(['status' => 'failed', 'updated_at' => now()])->save();
            $post->forceFill(['status' => 'removed', 'hide' => '1', 'updated_at' => now()])->save();

            return $this->error('GATEWAY_INIT_FAILED', 'Could not start secure payment. Please try again.', 502);
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'post_id' => $post->id,
            'gateway_url' => $url,
        ]);
    }

    /**
     * POST /checkout/ad-post
     *
     * Legacy pay-before-post endpoint retained for API compatibility.
     */
    public function adPost(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'ad_data' => ['required', 'array'],
            'ad_data.title' => ['required', 'string', 'min:3', 'max:150'],
            'ad_data.description' => ['required', 'string', 'min:10'],
            'ad_data.price' => ['required', 'integer', 'min:0'],
            'ad_data.category' => ['required', 'integer', 'exists:catagory_main,cat_id'],
            'ad_data.condition' => ['required', 'in:new,used'],
            'ad_data.sub_category' => ['nullable', 'integer', 'exists:catagory_sub,sub_cat_id'],
            'ad_data.phone' => ['nullable', 'string', 'max:50'],
            'ad_data.address' => ['nullable', 'string', 'max:500'],
            'ad_data.city' => ['nullable', 'string', 'max:50'],
            'ad_data.country' => ['nullable', 'string', 'max:50'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $amount = (float) ($plan->monthly_price ?? $plan->price ?? 0);

        if ($amount <= 0) {
            return $this->error('INVALID_PLAN', 'Selected plan has no price configured.', 422);
        }

        // Store ad data in transaction meta - will be created after payment
        $gateway = $this->manager->primary();
        $paymentAttrs = $this->paymentAttributes($request, $gateway->slug(), $data['ad_data'], $amount);
        $tx = Transaction::create([
            'seller_id' => $request->user()->id,
            'product_id' => 0, // not created yet
            'product_name' => 'Ad Posting: '.$data['ad_data']['title'],
            'plan_id' => $plan->id,
            'amount' => $amount,
            'transaction_gatway' => $gateway->slug(),
            'status' => 'pending',
            'purpose' => 'ad_post',
            'created_at' => now(),
            'updated_at' => now(),
            ...$paymentAttrs,
        ]);

        $url = $gateway->initiate($tx);

        if (!$url) {
            return $this->error('GATEWAY_INIT_FAILED', 'Could not start payment session. Please try again.', 502);
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'gateway_url' => $url,
        ]);
    }

    private function paymentAttributes(Request $request, string $gatewaySlug, array $purposeMeta = [], float $amount = 0): array
    {
        if ($gatewaySlug !== 'dgepay') {
            return !empty($purposeMeta) ? ['meta' => json_encode($purposeMeta)] : [];
        }

        $this->ensureDgePayUatUserAllowed($request);

        $validated = $request->validate([
            'policies_accepted' => ['required', 'accepted'],
            'payment_phone' => ['nullable', 'string', 'regex:/^01[3-9]\\d{8}$/'],
        ]);

        $idempotencyKey = trim((string) $request->header('Idempotency-Key', ''));
        validator(
            ['idempotency_key' => $idempotencyKey],
            ['idempotency_key' => ['required', 'string', 'max:100', 'unique:transaction,checkout_idempotency_key']],
        )->validate();

        // Prepare purpose meta with payment details
        $purposeMeta['_payment'] = array_filter([
            'phone' => $validated['payment_phone'] ?? null,
            'policy_accepted' => true,
        ]);

        return [
            'meta' => json_encode($purposeMeta),
            'policy_accepted_at' => now(),
            'policy_version' => (string) config('payments.policy_version', '1.0'),
            'currency' => (string) config('dgepay.currency', 'BDT'),
            'amount_minor' => (int) round($amount * 100),
            'checkout_idempotency_key' => $idempotencyKey,
        ];
    }

    private function ensureDgePayUatUserAllowed(Request $request): void
    {
        $host = strtolower((string) parse_url((string) config('dgepay.base_url'), PHP_URL_HOST));
        if (!app()->isProduction() || !str_contains($host, 'uat')) {
            return;
        }

        $allowed = array_map('intval', (array) config('dgepay.uat_allowed_user_ids', []));
        if (!in_array((int) $request->user()?->id, $allowed, true)) {
            throw new AuthorizationException('DGePay UAT checkout is restricted to approved test users.');
        }
    }
}
