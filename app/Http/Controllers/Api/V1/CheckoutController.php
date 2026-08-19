<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreAdRequest;
use App\Models\Option;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use App\Services\AdMutationService;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Buyer-initiated checkout endpoints.
 *
 *   POST /api/v1/checkout/plan/{planId}         → SSLCommerz page for a plan purchase
 *   POST /api/v1/checkout/ad-upgrade/{postId}   → SSLCommerz page for an ad boost
 *
 * All routes are authenticated (Sanctum). They persist a `transaction` row
 * so the IPN handler can locate it later, and return the SSLCommerz hosted
 * page URL that the SPA redirects to via `window.location`.
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
        $amount  = (float) ($cadence === 'annual'
            ? ($plan->annual_price  ?? $plan->price ?? 0)
            : ($plan->monthly_price ?? $plan->price ?? 0));

        if ($amount <= 0) {
            return $this->error('INVALID_PLAN', 'This plan has no price configured.', 422);
        }

        $tx = Transaction::create([
            'seller_id'          => $user->id,
            'product_id'         => 0,
            'product_name'       => 'Plan: ' . ($plan->name ?? "#{$plan->id}"),
            'plan_id'            => $plan->id,
            'amount'             => $amount,
            'transaction_gatway' => 'sslcommerz',
            'status'             => 'pending',
            'purpose'            => 'plan',
            'meta'               => json_encode(['cadence' => $cadence]),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $url = $this->manager->get('sslcommerz')->initiate($tx);
        if (! $url) {
            return $this->error('GATEWAY_INIT_FAILED', 'Could not start the payment session. Please try again.', 502);
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'gateway_url'    => $url,
        ]);
    }

    /** POST /checkout/ad-upgrade/{postId}  body: { featured?, urgent?, highlight? } */
    public function adUpgrade(int $postId, Request $request)
    {
        $data = $request->validate([
            'featured'  => ['sometimes', 'boolean'],
            'urgent'    => ['sometimes', 'boolean'],
            'highlight' => ['sometimes', 'boolean'],
        ]);

        $post = Post::findOrFail($postId);
        $this->authorize('update', $post);

        // Upgrade prices are site-wide settings set in the admin panel
        // (Settings → Premium upgrades). Defaults match the historical values.
        $settings = Option::pluck('option_value', 'option_name');
        $prices = [
            'featured'  => (float) ($settings['upgrade_featured_price']  ?? 200),
            'urgent'    => (float) ($settings['upgrade_urgent_price']    ?? 150),
            'highlight' => (float) ($settings['upgrade_highlight_price'] ?? 100),
        ];

        $amount = collect($prices)
            ->filter(fn ($price, $flag) => ! empty($data[$flag]))
            ->sum();

        if ($amount <= 0) {
            return $this->error('NO_UPGRADES_SELECTED', 'Please select at least one upgrade.', 422);
        }

        $tx = Transaction::create([
            'seller_id'          => $request->user()->id,
            'product_id'         => $post->id,
            'product_name'       => 'Ad boost: ' . ($post->product_name ?? "#{$post->id}"),
            'amount'             => $amount,
            'transaction_gatway' => 'sslcommerz',
            'status'             => 'pending',
            'purpose'            => 'ad_upgrade',
            'meta'               => json_encode($data),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $url = $this->manager->get('sslcommerz')->initiate($tx);
        if (! $url) {
            return $this->error('GATEWAY_INIT_FAILED', 'Could not start the payment session.', 502);
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'gateway_url'    => $url,
        ]);
    }

    /**
     * POST /checkout/product-purchase/{postId}
     *
     * "Buy Now" (option B). Buyer pays the full product price via SSLCommerz;
     * on success a real order is created (FulfilTransactionJob) and the
     * listing is marked sold. Option A (plain chat) never hits this endpoint.
     */
    public function productPurchase(int $postId, Request $request)
    {
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

        DB::transaction(function () use ($post, $buyer, $amount, &$tx, &$order) {
            $tx = Transaction::create([
                'seller_id'          => $buyer->id,
                'product_id'         => $post->id,
                'product_name'       => 'Purchase: ' . ($post->product_name ?? "#{$post->id}"),
                'amount'             => $amount,
                'transaction_gatway' => 'sslcommerz',
                'status'             => 'pending',
                'purpose'            => 'product_purchase',
                'meta'               => json_encode(['buyer_id' => $buyer->id, 'seller_id' => $post->user_id]),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            $order = Order::create([
                'product_id'     => $post->id,
                'buyer_id'       => $buyer->id,
                'seller_id'      => $post->user_id,
                'transaction_id' => $tx->id,
                'amount'         => $amount,
                'shipping_status'=> 'pending',
                'seller_paid'    => false,
            ]);
        });

        $url = $this->manager->get('sslcommerz')->initiate($tx);
        if (! $url) {
            return $this->error('GATEWAY_INIT_FAILED', 'Could not start the payment session.', 502);
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'order_id'       => $order->id,
            'gateway_url'    => $url,
        ]);
    }

    /** GET /checkout/transactions/{id} — poll for status after redirect back. */
    public function status(int $id, Request $request)
    {
        $tx = Transaction::where('id', $id)
                          ->where('seller_id', $request->user()->id)
                          ->firstOrFail();

        return $this->ok([
            'id'         => $tx->id,
            'status'     => $tx->status?->value ?? 'pending',
            'amount'     => (float) $tx->amount,
            'gateway'    => $tx->transaction_gatway,
            'purpose'    => $tx->purpose ?? null,
            'plan_id'    => $tx->plan_id ?? null,
            'post_id'    => $tx->product_id ?? null,
            'created_at' => (string) $tx->created_at,
        ]);
    }

    /** Pay per listing without consuming a subscription slot. */
    public function paidListing(StoreAdRequest $request)
    {
        $settings = Option::pluck('option_value', 'option_name');
        $amount = (float) ($settings['paid_listing_price'] ?? 500);
        if ($amount <= 0) {
            return $this->error('INVALID_LISTING_PRICE', 'Paid listing price is not configured.', 422);
        }

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
            'product_name' => 'Paid listing: ' . $post->product_name,
            'amount' => $amount,
            'transaction_gatway' => 'sslcommerz',
            'status' => 'pending',
            'purpose' => 'paid_listing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $url = $this->manager->get('sslcommerz')->initiate($tx);
        if (! $url) {
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
            'plan_id'             => ['required', 'exists:plans,id'],
            'ad_data'             => ['required', 'array'],
            'ad_data.title'       => ['required', 'string', 'min:3', 'max:150'],
            'ad_data.description' => ['required', 'string', 'min:10'],
            'ad_data.price'       => ['required', 'integer', 'min:0'],
            'ad_data.category'    => ['required', 'integer', 'exists:catagory_main,cat_id'],
            'ad_data.condition'   => ['required', 'in:new,used'],
            'ad_data.sub_category' => ['nullable', 'integer', 'exists:catagory_sub,sub_cat_id'],
            'ad_data.phone'       => ['nullable', 'string', 'max:50'],
            'ad_data.address'     => ['nullable', 'string', 'max:500'],
            'ad_data.city'        => ['nullable', 'string', 'max:50'],
            'ad_data.country'     => ['nullable', 'string', 'max:50'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $amount = (float) ($plan->monthly_price ?? $plan->price ?? 0);

        if ($amount <= 0) {
            return $this->error('INVALID_PLAN', 'Selected plan has no price configured.', 422);
        }

        // Store ad data in transaction meta - will be created after payment
        $tx = Transaction::create([
            'seller_id'          => $request->user()->id,
            'product_id'         => 0, // not created yet
            'product_name'       => 'Ad Posting: ' . $data['ad_data']['title'],
            'plan_id'            => $plan->id,
            'amount'             => $amount,
            'transaction_gatway' => 'sslcommerz',
            'status'             => 'pending',
            'purpose'            => 'ad_post',
            'meta'               => json_encode($data['ad_data']),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $url = $this->manager->get('sslcommerz')->initiate($tx);

        if (! $url) {
            return $this->error('GATEWAY_INIT_FAILED', 'Could not start payment session. Please try again.', 502);
        }

        return $this->ok([
            'transaction_id' => $tx->id,
            'gateway_url'    => $url,
        ]);
    }
}
