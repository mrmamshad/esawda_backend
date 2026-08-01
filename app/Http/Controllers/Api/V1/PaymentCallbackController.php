<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SSLCommerz callback handlers (public — no auth middleware). SSLCommerz
 * POSTs form-encoded fields to these URLs at the end of the checkout flow.
 *
 *   POST /api/v1/payments/sslcommerz/success   (browser POST) → validate & redirect to FE
 *   POST /api/v1/payments/sslcommerz/fail      (browser POST) → mark failed & redirect
 *   POST /api/v1/payments/sslcommerz/cancel    (browser POST) → mark cancel & redirect
 *   POST /api/v1/payments/sslcommerz/ipn       (server POST)  → verify & JSON reply
 *
 * The success/fail/cancel endpoints 302 the buyer to the Next.js frontend
 * so the SPA can show a receipt page and poll `/checkout/transactions/{id}`
 * for the definitive status.
 */
class PaymentCallbackController extends Controller
{
    public function __construct(private readonly PaymentManager $manager) {}

    public function success(Request $request): Response
    {
        return $this->handleAndRedirect($request, expected: 'success');
    }

    public function fail(Request $request): Response
    {
        return $this->handleAndRedirect($request, expected: 'failed');
    }

    public function cancel(Request $request): Response
    {
        return $this->handleAndRedirect($request, expected: 'cancel');
    }

    /** IPN — pure server-to-server, JSON reply, no redirect. */
    public function ipn(Request $request)
    {
        Log::info('SSLCommerz IPN received', $request->all());
        $tx = $this->processPayload($request->all());

        if ($tx && $tx->status === 'success') {
            $this->fulfil($tx);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => $tx?->status ?? 'unknown'], $tx ? 200 : 400);
    }

    /* --------------------------------------------------------------- */

    private function handleAndRedirect(Request $request, string $expected): Response
    {
        $tx = $this->processPayload($request->all());
        if ($tx && $tx->status === 'success') {
            $this->fulfil($tx);
        }

        $frontend = rtrim((string) config('sslcommerz.frontend_url', 'http://localhost:3000'), '/');
        $status   = $tx?->status ?? $expected;
        $target   = $frontend . '/membership/' . ($status === 'success' ? 'success' : 'failed')
                   . '?tx=' . urlencode((string) ($tx?->id ?? ''))
                   . '&status=' . urlencode($status);

        return response('', 302)->header('Location', $target);
    }

    private function processPayload(array $payload): ?Transaction
    {
        try {
            return $this->manager->get('sslcommerz')->handleCallback($payload);
        } catch (\Throwable $e) {
            Log::error('SSLCommerz callback processing failed: ' . $e->getMessage(), $payload);
            return null;
        }
    }

    /**
     * Post-success side-effects:
     *   - plan purchase → bump user's group_id + plan_expiry
     *   - ad upgrade   → flip featured/urgent/highlight on the post
     * Idempotent: safe to run repeatedly from IPN + success POST.
     */
    private function fulfil(Transaction $tx): void
    {
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
