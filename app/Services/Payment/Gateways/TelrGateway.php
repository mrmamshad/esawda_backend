<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/telr — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class TelrGateway extends AbstractGateway
{
    public function slug(): string
    {
        return 'telr';
    }

    public function label(): string
    {
        return 'Telr';
    }
}
