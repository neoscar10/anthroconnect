<?php

namespace App\Services\Membership;

use App\Models\User;
use App\Models\MembershipSetting;
use App\Services\Payment\PaymentManager;
use Exception;

class MembershipPurchaseService
{
    protected MembershipService $membershipService;
    protected PaymentManager $paymentManager;

    public function __construct(MembershipService $membershipService, PaymentManager $paymentManager)
    {
        $this->membershipService = $membershipService;
        $this->paymentManager = $paymentManager;
    }

    /**
     * Complete a membership purchase flow.
     */
    public function purchase(User $user, MembershipSetting $setting, array $cardData): array
    {
        // 1. Validation
        if ($user->isMember()) {
            throw new Exception("You are already an active member.");
        }

        if (!$setting->is_active) {
            throw new Exception("This membership plan is currently unavailable.");
        }

        // 2. Process payment via PaymentManager
        $paymentResult = $this->paymentManager->purchaseMembership($user, (float) $setting->price_inr, $cardData, $setting->id);

        if (!$paymentResult->success) {
            throw new Exception($paymentResult->message ?? "Payment failed. Please check your card details.");
        }

        // Reload user relationship to get active membership
        $user->load('membership');

        return [
            'success' => true,
            'membership' => $user->membership,
            'reference' => $paymentResult->reference,
            'masked_last4' => $paymentResult->meta['masked_last4'] ?? null,
        ];
    }
}

