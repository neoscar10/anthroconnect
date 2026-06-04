<?php

namespace App\Services\Payment;

use App\Models\PaymentSetting;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentSettingsService
{
    /**
     * Get all enabled gateways.
     */
    public function getEnabledGateways()
    {
        return PaymentSetting::enabled();
    }

    /**
     * Get the default gateway.
     */
    public function getDefaultGateway()
    {
        return PaymentSetting::default();
    }

    /**
     * Check if a gateway is enabled.
     */
    public function isGatewayEnabled(string $gatewayName): bool
    {
        $setting = PaymentSetting::where('gateway', $gatewayName)->first();
        return $setting ? (bool) $setting->is_enabled : false;
    }

    /**
     * Get standardized gateway display data.
     *
     * @return array<\App\DTOs\Payments\GatewayDisplayData>
     */
    public function getGatewayDisplayData(): array
    {
        return PaymentSetting::ordered()->map(function ($setting) {
            $description = $setting->gateway === 'razorpay' 
                ? 'Pay securely via Cards, Netbanking, UPI, or Wallets' 
                : ($setting->gateway === 'cashfree' ? 'Fast checkout using UPI, Cards, or Netbanking' : 'Local testing sandbox');

            return new \App\DTOs\Payments\GatewayDisplayData(
                code: $setting->gateway,
                name: $setting->display_name,
                description: $description,
                logo: $setting->gateway === 'razorpay' 
                    ? asset('images/gateways/razorpay.png') 
                    : ($setting->gateway === 'cashfree' ? asset('images/gateways/cashfree.png') : asset('images/gateways/dummy.png')),
                enabled: (bool) $setting->is_enabled,
                is_default: (bool) $setting->is_default
            );
        })->toArray();
    }

    /**
     * Enable a gateway.
     */
    public function enableGateway(string $gatewayName): void
    {
        $setting = PaymentSetting::where('gateway', $gatewayName)->first();
        if (!$setting) {
            throw new Exception("Gateway [{$gatewayName}] not found.");
        }

        $setting->update(['is_enabled' => true]);
    }

    /**
     * Disable a gateway.
     */
    public function disableGateway(string $gatewayName): void
    {
        $setting = PaymentSetting::where('gateway', $gatewayName)->first();
        if (!$setting) {
            throw new Exception("Gateway [{$gatewayName}] not found.");
        }

        // Rule 3: Default gateway must always be enabled.
        if ($setting->is_default) {
            throw new Exception("Cannot disable the default gateway. Set another gateway as default first.");
        }

        // Rule 1: At least one gateway must remain enabled.
        $enabledCount = PaymentSetting::where('is_enabled', true)->count();
        if ($enabledCount <= 1 && $setting->is_enabled) {
            throw new Exception("At least one gateway must remain enabled.");
        }

        $setting->update(['is_enabled' => false]);
    }

    /**
     * Set a gateway as the default.
     */
    public function setDefaultGateway(string $gatewayName): void
    {
        $setting = PaymentSetting::where('gateway', $gatewayName)->first();
        if (!$setting) {
            throw new Exception("Gateway [{$gatewayName}] not found.");
        }

        // Rule 4: Disabled gateway cannot be selected as default.
        if (!$setting->is_enabled) {
            throw new Exception("A disabled gateway cannot be set as the default gateway.");
        }

        DB::transaction(function () use ($setting) {
            // Rule 2: Only one gateway may be default.
            PaymentSetting::query()->update(['is_default' => false]);
            $setting->update(['is_default' => true]);
        });
    }

    /**
     * Update priority/sort order of gateways.
     */
    public function updatePriorities(array $orderedGateways): void
    {
        DB::transaction(function () use ($orderedGateways) {
            foreach ($orderedGateways as $index => $gatewayName) {
                PaymentSetting::where('gateway', $gatewayName)->update([
                    'sort_order' => $index + 1
                ]);
            }
        });
    }
}
