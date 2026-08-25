<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/paytm — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class PaytmGateway extends AbstractGateway
{
    public function slug(): string
    {
        return 'paytm';
    }

    public function label(): string
    {
        return 'Paytm';
    }
}
