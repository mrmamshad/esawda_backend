<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/mollie — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class MollieGateway extends AbstractGateway
{
    public function slug(): string
    {
        return 'mollie';
    }

    public function label(): string
    {
        return 'Mollie';
    }
}
