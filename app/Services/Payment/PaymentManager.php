<?php

namespace App\Services\Payment;

/**
 * Central registry / factory for payment gateway strategies.
 *
 * Every gateway that used to live under `includes/payments/{slug}/`
 * is now a class that implements `PaymentGatewayInterface`. This
 * manager keeps the mapping in one place so controllers never
 * `require_once` gateway files.
 *
 *   $gw = app(PaymentManager::class)->get('paypal');
 *   return $gw->initiate($transaction);
 */
class PaymentManager
{
    /**
     * slug => concrete class.
     *
     * As of 2026-07 the platform ships with SSLCommerz as the sole active
     * gateway (Bangladesh market focus). The other 15 legacy gateways are
     * left as commented-out entries; their source files are still present
     * under App\Services\Payment\Gateways\* and can be re-enabled here.
     */
    protected array $registry = [
        'sslcommerz' => Gateways\SSLCommerzGateway::class,

        // ---- Deprecated (kept for reference) ---------------------------------
        // '2checkout'     => Gateways\TwoCheckoutGateway::class,
        // 'ccavenue'      => Gateways\CcavenueGateway::class,
        // 'flutterwave'   => Gateways\FlutterwaveGateway::class,
        // 'iyzico'        => Gateways\IyzicoGateway::class,
        // 'midtrans'      => Gateways\MidtransGateway::class,
        // 'mollie'        => Gateways\MollieGateway::class,
        // 'paypal'        => Gateways\PaypalGateway::class,
        // 'paystack'      => Gateways\PaystackGateway::class,
        // 'paytabs'       => Gateways\PaytabsGateway::class,
        // 'paytm'         => Gateways\PaytmGateway::class,
        // 'payumoney'     => Gateways\PayumoneyGateway::class,
        // 'razorpay'      => Gateways\RazorpayGateway::class,
        // 'stripe'        => Gateways\StripeGateway::class,
        // 'telr'          => Gateways\TelrGateway::class,
        // 'wire_transfer' => Gateways\WireTransferGateway::class,
    ];

    public function get(string $slug): PaymentGatewayInterface
    {
        if (!isset($this->registry[$slug])) {
            throw new \InvalidArgumentException("Unknown payment gateway: $slug");
        }

        return app($this->registry[$slug]);
    }

    /** @return array<string,string> slug => label for UI selection */
    public function available(): array
    {
        $out = [];
        foreach ($this->registry as $slug => $class) {
            $out[$slug] = app($class)->label();
        }

        return $out;
    }
}
