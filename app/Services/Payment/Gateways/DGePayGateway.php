<?php

namespace App\Services\Payment\Gateways;

use App\Enums\TransactionStatus;
use App\Models\PaymentEvent;
use App\Models\Transaction;
use App\Services\Payment\DGePay\DGePayClient;
use App\Services\Payment\DGePay\DGePayException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DGePayGateway extends AbstractGateway
{
    public function __construct(private readonly DGePayClient $client) {}

    public function slug(): string
    {
        return 'dgepay';
    }

    public function label(): string
    {
        return 'DGePay';
    }

    public function initiate(Transaction $tx): mixed
    {
        if (!config('payments.gateways.dgepay.accept_new', false)) {
            Log::warning('DGePay initiation blocked because new payments are disabled.');

            return null;
        }

        if (empty($tx->payment_id)) {
            $tx->payment_id = 'ES-'.strtoupper((string) Str::ulid());
        }
        $tx->forceFill([
            'transaction_gatway' => $this->slug(),
            'status' => TransactionStatus::Pending,
            'currency' => (string) config('dgepay.currency', 'BDT'),
            'amount_minor' => (int) round((float) $tx->amount * 100),
        ])->save();

        try {
            $response = $this->client->initiate($this->payload($tx));
            $url = (string) data_get($response, 'data.webview_url', '');
            if ($url === '') {
                throw new DGePayException('DGePay initiation response did not contain a webview URL.');
            }

            $tx->forceFill(['gateway_initiated_at' => now(), 'updated_at' => now()])->save();

            return $this->client->assertSafeWebviewUrl($url);
        } catch (\Throwable $e) {
            Log::error('DGePay initiation failed', [
                'transaction_id' => $tx->id,
                'error_type' => $e::class,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function handleCallback(array $payload): Transaction
    {
        if (!config('payments.gateways.dgepay.accept_callbacks', true)) {
            throw new DGePayException('DGePay callbacks are disabled.');
        }

        $encrypted = $payload['data'] ?? null;
        if (!is_string($encrypted) || $encrypted === '') {
            throw new DGePayException('DGePay return data is missing.');
        }
        $decoded = $this->client->decryptReturn($encrypted);
        $data = is_array($decoded['data'] ?? null) ? $decoded['data'] : $decoded;
        $reference = (string) ($data['unique_txn_id'] ?? '');
        if ($reference === '') {
            throw new DGePayException('DGePay return data did not contain a transaction reference.');
        }

        $tx = Transaction::query()
            ->where('transaction_gatway', $this->slug())
            ->where('payment_id', $reference)
            ->firstOrFail();

        $event = PaymentEvent::firstOrCreate(
            ['gateway' => $this->slug(), 'dedupe_key' => hash('sha256', $encrypted)],
            ['transaction_id' => $tx->id, 'event_type' => 'browser_return'],
        );

        // Decrypted browser data is only a signal; the server-to-server status
        // endpoint is always authoritative.
        $this->verify($tx);
        $event->forceFill([
            'status_code' => $tx->fresh()->gateway_status_code,
            'processed_at' => now(),
        ])->save();

        return $tx->fresh();
    }

    public function verify(Transaction $tx): bool
    {
        if ($tx->transaction_gatway !== $this->slug() || empty($tx->payment_id)) {
            return false;
        }

        $response = $this->client->checkStatus((string) $tx->payment_id);
        $data = is_array($response['data'] ?? null) ? $response['data'] : $response;
        $reference = (string) ($data['unique_txn_id'] ?? '');
        if (!hash_equals((string) $tx->payment_id, $reference)) {
            throw new DGePayException('DGePay status reference did not match the local transaction.');
        }

        if (isset($data['amount'])) {
            $paidMinor = (int) round((float) $data['amount'] * 100);
            $expectedMinor = (int) ($tx->amount_minor ?: round((float) $tx->amount * 100));
            if ($paidMinor !== $expectedMinor) {
                throw new DGePayException('DGePay status amount did not match the local transaction.');
            }
        }

        $statusCode = (string) ($data['status_code'] ?? $response['status_code'] ?? '');
        // Live UAT observation: DGePay returns 8 with message
        // "TRANSACTION CANCELLED" when the payer abandons the hosted page.
        // Unmapped codes must fail closed as pending (reconciliation keeps
        // retrying until the gateway settles), never silently succeed.
        $mapped = match ($statusCode) {
            '3' => TransactionStatus::Success,
            '4', '5', '8' => TransactionStatus::Failed,
            '6' => TransactionStatus::Refunded,
            default => TransactionStatus::Pending,
        };

        DB::transaction(function () use ($tx, $data, $statusCode, $mapped): void {
            $locked = Transaction::query()->lockForUpdate()->findOrFail($tx->id);
            $current = $locked->status;

            // Never let delayed or replayed responses downgrade terminal money states.
            if ($current === TransactionStatus::Refunded) {
                $mappedStatus = TransactionStatus::Refunded;
            } elseif ($current === TransactionStatus::Success && $mapped !== TransactionStatus::Refunded) {
                $mappedStatus = TransactionStatus::Success;
            } else {
                $mappedStatus = $mapped;
            }

            $locked->forceFill([
                'status' => $mappedStatus,
                'gateway_status_code' => $statusCode ?: null,
                'gateway_status_message' => isset($data['message']) ? Str::limit((string) $data['message'], 500, '') : null,
                'gateway_transaction_id' => $data['txn_id'] ?? null,
                'gateway_transaction_number' => $data['txn_number'] ?? null,
                'gateway_third_party_transaction_number' => $data['third_party_txn_number'] ?? null,
                'transaction_method' => isset($data['payment_method']) ? Str::limit((string) $data['payment_method'], 20, '') : null,
                'last_verified_at' => now(),
                'updated_at' => now(),
            ])->save();
        }, 3);

        return $tx->fresh()->status === TransactionStatus::Success;
    }

    private function payload(Transaction $tx): array
    {
        $meta = json_decode((string) ($tx->meta ?? '{}'), true) ?: [];
        $phone = $this->normalisePhone(data_get($meta, '_payment.phone') ?? $tx->seller?->phone);

        return [
            'amount' => (float) number_format((float) $tx->amount, 2, '.', ''),
            'customer_token' => null,
            'meta_data' => [
                'custom_field_1' => (string) $tx->id,
                'custom_field_2' => (string) ($tx->purpose ?? 'payment'),
                'custom_field_3' => (string) ($tx->seller_id ?? ''),
            ],
            'note' => Str::limit((string) ($tx->product_name ?? 'eSawda payment'), 150, ''),
            'payee_information' => $phone ? ['dial_code' => '+88', 'phone_number' => $phone] : null,
            'payment_method' => null,
            'redirect_url' => route('payments.dgepay.return'),
            'unique_txn_id' => (string) $tx->payment_id,
        ];
    }

    private function normalisePhone(mixed $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';
        if (strlen($digits) === 13 && str_starts_with($digits, '880')) {
            $digits = '0'.substr($digits, 3);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            $digits = '0'.$digits;
        }

        return preg_match('/^01[3-9]\d{8}$/', $digits) ? $digits : null;
    }
}
