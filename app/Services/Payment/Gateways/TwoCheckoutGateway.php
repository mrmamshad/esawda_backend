<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/2checkout — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class TwoCheckoutGateway extends AbstractGateway
{
    public function slug(): string
    {
        return '2checkout';
    }

    public function label(): string
    {
        return '2Checkout';
    }
}
