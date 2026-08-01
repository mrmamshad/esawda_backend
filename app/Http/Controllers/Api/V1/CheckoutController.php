<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;

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
    public function __construct(private readonly PaymentManager $manager) {}

    /** POST /checkout/plan/{planId} */
    public function plan(int $planId, Request $request)
    {
        $plan = Plan::findOrFail($planId);
        $user = $request->user();

        $cadence = $request->input('cadence', 'monthly');
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

        // Static per-upgrade price. In a real deploy pull these from settings.
        $prices = ['featured' => 200, 'urgent' => 150, 'highlight' => 100];

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

    /** GET /checkout/transactions/{id} — poll for status after redirect back. */
    public function status(int $id, Request $request)
    {
        $tx = Transaction::where('id', $id)
                          ->where('seller_id', $request->user()->id)
                          ->firstOrFail();

        return $this->ok([
            'id'         => $tx->id,
            'status'     => $tx->status,
            'amount'     => (float) $tx->amount,
            'gateway'    => $tx->transaction_gatway,
            'purpose'    => $tx->purpose ?? null,
            'plan_id'    => $tx->plan_id ?? null,
            'post_id'    => $tx->product_id ?? null,
            'created_at' => (string) $tx->created_at,
        ]);
    }
}
