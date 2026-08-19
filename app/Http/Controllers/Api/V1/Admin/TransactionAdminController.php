<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FulfilTransactionJob;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Mail\MailService;
use Illuminate\Http\Request;

class TransactionAdminController extends Controller
{
    public function __construct(private readonly MailService $mail) {}

    public function index(Request $request)
    {
        $q = Transaction::query()->with('seller:id,username,email');

        if ($s = $request->query('status'))  $q->where('status', $s);
        if ($g = $request->query('gateway')) $q->where('transaction_gatway', $g);
        if ($p = $request->query('purpose')) $q->where('purpose', $p);

        $q->orderByDesc('id');
        return $this->ok($q->paginate((int) min(100, max(1, (int) $request->query('per_page', 20)))));
    }

    public function show(int $id)
    {
        return $this->ok(Transaction::with('seller')->findOrFail($id));
    }

    public function refund(int $id)
    {
        $tx = Transaction::findOrFail($id);
        $tx->forceFill(['status' => 'refunded', 'updated_at' => now()])->save();

        $order = Order::where('transaction_id', $tx->id)->first();
        $this->mail->refundedToBuyer($tx, $order);

        return $this->ok(['message' => 'Transaction marked refunded.', 'transaction' => $tx]);
    }

    public function markPaid(int $id)
    {
        $tx = Transaction::findOrFail($id);
        $tx->forceFill(['status' => 'success', 'updated_at' => now()])->save();

        // Mirror what the gateway callback does on a successful payment so
        // the order/ad side effects and their emails fire consistently.
        try {
            FulfilTransactionJob::dispatchSync($tx->id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Fulfilment skipped for manually marked transaction', [
                'transaction_id' => $tx->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->ok(['message' => 'Transaction marked paid.', 'transaction' => $tx]);
    }
}
