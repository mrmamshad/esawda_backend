<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/iyzico — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class IyzicoGateway extends AbstractGateway
{
    public function slug(): string
    {
        return 'iyzico';
    }

    public function label(): string
    {
        return 'iyzico';
    }
}
