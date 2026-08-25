<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/flutterwave — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class FlutterwaveGateway extends AbstractGateway
{
    public function slug(): string
    {
        return 'flutterwave';
    }

    public function label(): string
    {
        return 'Flutterwave';
    }
}
