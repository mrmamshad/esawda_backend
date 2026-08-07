<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;

/**
 * Legacy: `php/payment.php` + `php/ipn.php`. Dispatches to whichever
 * gateway the buyer chose. Never call gateway-specific SDKs directly —
 * always go through `PaymentManager::get($slug)`.
 */
class PaymentController extends Controller
{
    public function __construct(private PaymentManager $mgr) {}

    /** Buyer initiates payment. Legacy URL: /payment/{token}/{i}/{status?} */
    public function index(Request $request, ?string $access_token = null, ?string $i = null, ?string $status = null)
    {
        $tx = Transaction::findOrFail((int) $access_token);

        // If a status is supplied, we're rendering the result page after
        // the gateway redirected here (or after wire_transfer instructions).
        if ($status) {
            return app(\App\Services\ThemeRenderer::class)->render('payment', [
                'transaction' => $tx,
                'status'      => $status,
            ]);
        }

        $gateway = $this->mgr->get($i ?: 'wire_transfer');
        $redirect = $gateway->initiate($tx);
        return is_string($redirect) ? redirect($redirect) : $redirect;
    }

    /** Legacy IPN URL: /ipn/{i}/{access_token} — gateway callback. */
    public function ipn(Request $request, ?string $i = null)
    {
        $gateway = $this->mgr->get($i ?: abort(400, 'Missing gateway slug'));
        $payload = array_merge($request->query(), $request->post());
        $tx = $gateway->handleCallback($payload);
        // Redirect the user to a friendly result page.
        return redirect()->route('payment', [
            'access_token' => $tx->id,
            'i'            => $i,
            'status'       => $tx->status === \App\Enums\TransactionStatus::Success
                ? 'success'
                : ($tx->status === \App\Enums\TransactionStatus::Cancel ? 'cancel' : 'pending'),
        ]);
    }
}
