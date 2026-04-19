<?php

namespace App\Services\Membership;

use App\Models\User;
use App\Models\MembershipSetting;
use App\Models\UserMembership;
use App\Services\Payment\DummyPaymentGateway;
use Exception;
use Illuminate\Support\Facades\DB;

class MembershipPurchaseService
{
    protected MembershipService $membershipService;
    protected DummyPaymentGateway $paymentGateway;

    public function __construct(MembershipService $membershipService, DummyPaymentGateway $paymentGateway)
    {
        $this->membershipService = $membershipService;
        $this->paymentGateway = $paymentGateway;
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

        // 2. Process dummy payment
        $paymentResult = $this->paymentGateway->process($cardData, $setting->price_inr);

        if (!$paymentResult['success']) {
            throw new Exception($paymentResult['message'] ?? "Payment failed. Please check your card details.");
        }

        // 3. Activate membership on success
        return DB::transaction(function () use ($user, $setting, $paymentResult) {
            $membership = $this->membershipService->activateMembership(
                $user, 
                $setting, 
                $paymentResult['reference']
            );

            return [
                'success' => true,
                'membership' => $membership,
                'reference' => $paymentResult['reference'],
                'masked_last4' => $paymentResult['masked_last4'],
            ];
        });
    }
}
