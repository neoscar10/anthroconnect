<?php

namespace App\Services\Payment\Actions;

use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payment\Exceptions\PaymentVerificationException;
use Illuminate\Support\Facades\Log;

class VerifyPaymentAction
{
    protected VerifyRazorpayPaymentAction $verifyRazorpay;
    protected MarkTransactionFailedAction $markFailed;

    public function __construct(
        VerifyRazorpayPaymentAction $verifyRazorpay,
        MarkTransactionFailedAction $markFailed
    ) {
        $this->verifyRazorpay = $verifyRazorpay;
        $this->markFailed = $markFailed;
    }

    public function execute(User $user, array $params): bool
    {
        $reference = $params['transaction_reference'] ?? null;
        if (!$reference) {
            throw new PaymentVerificationException('Transaction reference is required.');
        }

        $transaction = PaymentTransaction::where('reference', $reference)->first();

        if (!$transaction) {
            throw new PaymentVerificationException("No transaction found matching reference [{$reference}].");
        }

        // Validate Ownership
        if ($transaction->user_id !== $user->id) {
            Log::warning("User [{$user->id}] attempted to verify transaction [{$reference}] owned by User [{$transaction->user_id}].");
            throw new PaymentVerificationException('Unauthorized transaction access.');
        }

        // Check if already captured
        if ($transaction->isCaptured()) {
            return true;
        }

        // Verify with Razorpay
        $verification = $this->verifyRazorpay->execute($transaction, $params);

        if ($verification->success) {
            $transaction->gateway_payment_id = $params['razorpay_payment_id'];
            $transaction->gateway_signature = $params['razorpay_signature'];
            $transaction->meta = array_merge($transaction->meta ?? [], ['frontend_callback' => $params]);
            $transaction->transitionTo(\App\Enums\Payment\PaymentStatus::AUTHORIZED);

            return true;
        } else {
            $this->markFailed->execute($transaction, $verification->message ?? 'Signature verification failed.');
            throw new PaymentVerificationException($verification->message ?? 'Signature verification failed.');
        }
    }
}
