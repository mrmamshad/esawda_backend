<?php

namespace Tests\Unit;

use App\Services\Payment\Gateways\SSLCommerzGateway;
use App\Services\Payment\PaymentManager;
use Tests\TestCase;

/**
 * The platform now ships SSLCommerz as the only registered gateway.
 * All other legacy gateway classes still exist under
 * App\Services\Payment\Gateways\* but are commented out in the registry.
 */
class PaymentManagerTest extends TestCase
{
    public function test_only_sslcommerz_is_registered(): void
    {
        $available = (new PaymentManager())->available();
        $this->assertCount(1, $available);
        $this->assertArrayHasKey('sslcommerz', $available);
    }

    public function test_resolves_sslcommerz(): void
    {
        $gw = (new PaymentManager())->get('sslcommerz');
        $this->assertInstanceOf(SSLCommerzGateway::class, $gw);
        $this->assertSame('sslcommerz', $gw->slug());
        $this->assertNotEmpty($gw->label());
    }

    public function test_rejects_unknown_slug(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new PaymentManager())->get('paypal');
    }
}
