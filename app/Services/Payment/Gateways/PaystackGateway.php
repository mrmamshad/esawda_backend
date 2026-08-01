<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/paystack — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class PaystackGateway extends AbstractGateway
{
    public function slug(): string  { return 'paystack'; }
    public function label(): string { return 'Paystack'; }

    // TODO(migration): implement gateway-specific SDK flow from
    // includes/payments/paystack/*.php
}
