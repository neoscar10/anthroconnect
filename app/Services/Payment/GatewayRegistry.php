<?php

namespace App\Services\Payment;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Models\PaymentSetting;
use App\Services\Payment\Exceptions\UnsupportedGatewayException;
use App\Services\Payment\Gateways\DummyPaymentGateway;
use App\Services\Payment\Gateways\RazorpayGateway;
use App\Services\Payment\Gateways\CashfreeGateway;
use Exception;

class GatewayRegistry
{
    /**
     * Resolve the gateway implementation by name.
     */
    public function resolve(string $gatewayName): PaymentGatewayInterface
    {
        // Dummy is always supported as the local fallback/testing gateway
        if ($gatewayName === 'dummy') {
            return app(DummyPaymentGateway::class);
        }

        $setting = PaymentSetting::where('gateway', $gatewayName)->first();
        
        if (!$setting || !$setting->is_enabled) {
            throw new UnsupportedGatewayException("Gateway [{$gatewayName}] is not supported or is disabled.");
        }

        if ($gatewayName === 'razorpay') {
            return app(RazorpayGateway::class);
        }

        if ($gatewayName === 'cashfree') {
            return app(CashfreeGateway::class);
        }

        throw new UnsupportedGatewayException("Gateway [{$gatewayName}] implementation is not registered.");
    }

    /**
     * Get list of enabled gateway identifiers.
     */
    public function getEnabledGateways(): array
    {
        $enabled = PaymentSetting::enabled()->pluck('gateway')->toArray();
        
        // Ensure dummy is always available if database settings are empty/unseeded
        if (empty($enabled)) {
            $enabled[] = 'dummy';
        }
        
        return $enabled;
    }

    /**
     * Get the default gateway identifier.
     */
    public function getDefaultGateway(): string
    {
        // Support runtime override via config (primarily for tests)
        $configDefault = config('payments.default_gateway');
        if ($configDefault === 'dummy') {
            return 'dummy';
        }

        $default = PaymentSetting::default();
        return $default ? $default->gateway : ($configDefault ?? 'dummy');
    }
}
