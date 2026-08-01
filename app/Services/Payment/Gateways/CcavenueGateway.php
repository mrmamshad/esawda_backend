<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/ccavenue — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class CcavenueGateway extends AbstractGateway
{
    public function slug(): string  { return 'ccavenue'; }
    public function label(): string { return 'CCAvenue'; }

    // TODO(migration): implement gateway-specific SDK flow from
    // includes/payments/ccavenue/*.php
}
