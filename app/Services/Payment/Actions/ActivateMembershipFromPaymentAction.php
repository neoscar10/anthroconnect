<?php

namespace App\Services\Payment\Actions;

use App\Models\PaymentTransaction;
use App\Models\MembershipSetting;
use App\Services\Membership\MembershipService;
use Exception;

class ActivateMembershipFromPaymentAction
{
    protected MembershipService $membershipService;

    public function __construct(MembershipService $membershipService)
    {
        $this->membershipService = $membershipService;
    }

    public function execute(PaymentTransaction $transaction): void
    {
        $user = $transaction->user;
        if (!$user) {
            throw new Exception("Transaction user not found.");
        }

        // Get setting ID from meta
        $metaSettingId = $transaction->meta['membership_setting_id'] ?? null;

        if ($metaSettingId) {
            $setting = MembershipSetting::find($metaSettingId);
        } else {
            $setting = $this->membershipService->getCurrentSettings();
        }

        if (!$setting) {
            throw new Exception("Membership configuration is missing for activation.");
        }

        $this->membershipService->activateMembership(
            $user,
            $setting,
            $transaction->reference
        );
    }
}
