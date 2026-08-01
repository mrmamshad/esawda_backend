<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/midtrans — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class MidtransGateway extends AbstractGateway
{
    public function slug(): string  { return 'midtrans'; }
    public function label(): string { return 'Midtrans'; }

    // TODO(migration): implement gateway-specific SDK flow from
    // includes/payments/midtrans/*.php
}
