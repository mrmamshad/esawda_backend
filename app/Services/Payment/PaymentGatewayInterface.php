<?php

namespace App\Services\Payment;

use App\Models\Transaction;

/**
 * Contract every payment integration must implement. Replaces the
 * scattered includes/payments/{gateway}/config.php files from the legacy
 * codebase with a single strategy interface. Implementations live in
 * App\Services\Payment\Gateways\{Name}Gateway.
 */
interface PaymentGatewayInterface
{
    /** Canonical slug used in URLs, DB, and config. */
    public function slug(): string;

    /** Human-readable label shown to buyers. */
    public function label(): string;

    /** Redirect user to the gateway's checkout page. */
    public function initiate(Transaction $tx): mixed;

    /** Handle the gateway's IPN / webhook callback. */
    public function handleCallback(array $payload): Transaction;

    /** Verify a transaction status directly with the gateway API. */
    public function verify(Transaction $tx): bool;
}
