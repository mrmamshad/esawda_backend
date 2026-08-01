<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use App\Services\AuthService;
use App\Services\Payment\PaymentManager;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/** Legacy `php/membership.php` — upgrade to paid plan. */
class MembershipController extends Controller
{
    public function __construct(
        private AuthService $auth,
        private ThemeRenderer $theme,
        private PaymentManager $pay,
    ) {}

    public function index(Request $request, ?string $change_plan = null)
    {
        if (! $this->auth->check($request)) return redirect()->route('auth.login');

        // POST: buyer picked a plan + gateway → create Transaction, redirect to gateway.
        if ($request->isMethod('post') && $request->filled('plan_id')) {
            $data = $request->validate([
                'plan_id'   => 'required|integer',
                'frequency' => 'required|in:MONTHLY,YEARLY,LIFETIME',
                'gateway'   => 'required|string',
            ]);
            $plan = Plan::findOrFail($data['plan_id']);
            $priceCol = strtolower($data['frequency']) . '_price';
            $amount = (float) ($plan->$priceCol ?? 0);

            $tx = Transaction::create([
                'seller_id'             => session('user.id'),
                'product_name'          => "Plan: {$plan->name}",
                'amount'                => $amount,
                'base_amount'           => $amount,
                'status'                => 'pending',
                'frequency'             => $data['frequency'],
                'transaction_gatway'    => $data['gateway'],
                'transaction_time'      => time(),
                'transaction_ip'        => $request->ip(),
            ]);
            return redirect()->route('payment', [
                'access_token' => $tx->id,
                'i'            => $data['gateway'],
            ]);
        }

        try {
            return $this->theme->render('membership_plan', [
                'plans'    => Plan::where('status', 1)->get(),
                'gateways' => $this->pay->available(),
            ]);
        } catch (\Throwable) {
            return view('placeholder', ['legacy' => 'membership.php', 'action' => 'plan']);
        }
    }
}
