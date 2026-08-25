<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/payumoney — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class PayumoneyGateway extends AbstractGateway
{
    public function slug(): string
    {
        return 'payumoney';
    }

    public function label(): string
    {
        return 'PayU Money';
    }
}
