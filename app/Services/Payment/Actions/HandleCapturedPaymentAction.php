<?php

namespace App\Services\Payment\Actions;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleCapturedPaymentAction
{
    protected ActivateMembershipFromPaymentAction $activateMembership;

    public function __construct(ActivateMembershipFromPaymentAction $activateMembership)
    {
        $this->activateMembership = $activateMembership;
    }

    public function execute(PaymentTransaction $transaction): void
    {
        if (!$transaction->isCaptured()) {
            Log::warning("Cannot activate membership because transaction [{$transaction->reference}] is not captured.");
            return;
        }

        $user = $transaction->user;
        if ($user && $user->isMember()) {
            Log::info("User [{$user->id}] already has active membership. Skipping activation for transaction [{$transaction->reference}].");
            return;
        }

        DB::transaction(function () use ($transaction) {
            $this->activateMembership->execute($transaction);
        });
    }
}
