<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/paytabs — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class PaytabsGateway extends AbstractGateway
{
    public function slug(): string  { return 'paytabs'; }
    public function label(): string { return 'PayTabs'; }

    // TODO(migration): implement gateway-specific SDK flow from
    // includes/payments/paytabs/*.php
}
