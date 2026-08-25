<?php

namespace App\Services\Payment\Gateways;

/**
 * Legacy: includes/payments/razorpay — ported to Laravel service.
 * All gateway-specific SDK calls belong inside initiate() / handleCallback().
 */
class RazorpayGateway extends AbstractGateway
{
    public function slug(): string
    {
        return 'razorpay';
    }

    public function label(): string
    {
        return 'Razorpay';
    }
}
