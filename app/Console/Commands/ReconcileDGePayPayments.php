<?php

namespace App\Console\Commands;

use App\Enums\TransactionStatus;
use App\Jobs\FulfilTransactionJob;
use App\Models\Transaction;
use App\Services\Payment\PaymentManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcileDGePayPayments extends Command
{
    protected $signature = 'payments:reconcile-dgepay {--limit=100 : Maximum pending transactions to check}';

    protected $description = 'Verify pending DGePay transactions with the authoritative status API';

    public function handle(PaymentManager $payments): int
    {
        $gateway = $payments->get('dgepay');
        $transactions = Transaction::query()
            ->where('transaction_gatway', 'dgepay')
            ->where('status', TransactionStatus::Pending)
            ->whereNotNull('payment_id')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('id')
            ->limit(max(1, min(500, (int) $this->option('limit'))))
            ->get();

        $verified = 0;
        $failed = 0;
        foreach ($transactions as $transaction) {
            try {
                if ($gateway->verify($transaction)) {
                    FulfilTransactionJob::dispatch($transaction->id);
                }
                $verified++;
            } catch (\Throwable $e) {
                $failed++;
                Log::warning('DGePay reconciliation failed', [
                    'transaction_id' => $transaction->id,
                    'error_type' => $e::class,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("DGePay reconciliation checked {$verified} transaction(s); {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
