<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionAdminController extends Controller
{
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
        return $this->ok(['message' => 'Transaction marked refunded.', 'transaction' => $tx]);
    }

    public function markPaid(int $id)
    {
        $tx = Transaction::findOrFail($id);
        $tx->forceFill(['status' => 'success', 'updated_at' => now()])->save();
        return $this->ok(['message' => 'Transaction marked paid.', 'transaction' => $tx]);
    }
}
