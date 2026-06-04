<?php

namespace App\Livewire\Admin\Settings;

use App\Models\PaymentSetting;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentSettingsService;
use App\Enums\Payment\PaymentStatus;
use Livewire\Component;
use Exception;

class PaymentSettings extends Component
{
    public $errorMessage = null;
    public $successMessage = null;

    public function toggleGateway(string $gatewayName)
    {
        $this->resetMessages();
        $service = app(PaymentSettingsService::class);
        $setting = PaymentSetting::where('gateway', $gatewayName)->first();

        if (!$setting) {
            $this->errorMessage = "Gateway not found.";
            return;
        }

        try {
            if ($setting->is_enabled) {
                $service->disableGateway($gatewayName);
                $this->successMessage = "Disabled gateway: " . $setting->display_name;
            } else {
                $service->enableGateway($gatewayName);
                $this->successMessage = "Enabled gateway: " . $setting->display_name;
            }
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function setDefault(string $gatewayName)
    {
        $this->resetMessages();
        $service = app(PaymentSettingsService::class);
        $setting = PaymentSetting::where('gateway', $gatewayName)->first();

        if (!$setting) {
            $this->errorMessage = "Gateway not found.";
            return;
        }

        try {
            $service->setDefaultGateway($gatewayName);
            $this->successMessage = "Set " . $setting->display_name . " as default gateway.";
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function moveUp(string $gatewayName)
    {
        $this->resetMessages();
        $settings = PaymentSetting::ordered()->pluck('gateway')->toArray();
        $index = array_search($gatewayName, $settings);
        if ($index !== false && $index > 0) {
            $temp = $settings[$index];
            $settings[$index] = $settings[$index - 1];
            $settings[$index - 1] = $temp;
            app(PaymentSettingsService::class)->updatePriorities($settings);
            $this->successMessage = "Priority updated.";
        }
    }

    public function moveDown(string $gatewayName)
    {
        $this->resetMessages();
        $settings = PaymentSetting::ordered()->pluck('gateway')->toArray();
        $index = array_search($gatewayName, $settings);
        if ($index !== false && $index < count($settings) - 1) {
            $temp = $settings[$index];
            $settings[$index] = $settings[$index + 1];
            $settings[$index + 1] = $temp;
            app(PaymentSettingsService::class)->updatePriorities($settings);
            $this->successMessage = "Priority updated.";
        }
    }

    protected function resetMessages()
    {
        $this->errorMessage = null;
        $this->successMessage = null;
    }

    public function render()
    {
        // 1. Get current gateway settings
        $gateways = PaymentSetting::ordered();

        // 2. Fetch payment transactions analytics
        $totalRevenue = PaymentTransaction::where('status', PaymentStatus::CAPTURED)->sum('amount');
        $successfulPayments = PaymentTransaction::where('status', PaymentStatus::CAPTURED)->count();
        $failedPayments = PaymentTransaction::where('status', PaymentStatus::FAILED)->count();
        $refundedPayments = PaymentTransaction::where('status', PaymentStatus::REFUNDED)->count();
        $transactionsToday = PaymentTransaction::whereDate('created_at', today())->count();
        $transactionsThisMonth = PaymentTransaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 3. Gateway breakdown calculations
        $breakdown = [];
        foreach ($gateways as $gw) {
            $gwTotal = PaymentTransaction::where('gateway', $gw->gateway)
                ->where('status', PaymentStatus::CAPTURED)
                ->sum('amount');
            $gwSuccess = PaymentTransaction::where('gateway', $gw->gateway)
                ->where('status', PaymentStatus::CAPTURED)
                ->count();
            $gwTotalCount = PaymentTransaction::where('gateway', $gw->gateway)->count();
            $successRate = $gwTotalCount > 0 ? round(($gwSuccess / $gwTotalCount) * 100, 1) : 0.0;

            $breakdown[] = [
                'display_name' => $gw->display_name,
                'gateway' => $gw->gateway,
                'revenue' => $gwTotal,
                'count' => $gwTotalCount,
                'success_rate' => $successRate,
            ];
        }

        return view('livewire.admin.settings.payment-settings', [
            'gateways' => $gateways,
            'totalRevenue' => $totalRevenue,
            'successfulPayments' => $successfulPayments,
            'failedPayments' => $failedPayments,
            'refundedPayments' => $refundedPayments,
            'transactionsToday' => $transactionsToday,
            'transactionsThisMonth' => $transactionsThisMonth,
            'breakdown' => $breakdown,
        ])->layout('layouts.admin');
    }
}
