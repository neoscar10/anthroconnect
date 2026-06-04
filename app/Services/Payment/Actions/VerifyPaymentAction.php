<?php

namespace App\Services\Payment\Actions;

use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payment\Exceptions\PaymentVerificationException;
use App\Services\Payment\PaymentManager;
use Illuminate\Support\Facades\Log;

class VerifyPaymentAction
{
    protected PaymentManager $paymentManager;
    protected MarkTransactionFailedAction $markFailed;

    public function __construct(
        PaymentManager $paymentManager,
        MarkTransactionFailedAction $markFailed
    ) {
        $this->paymentManager = $paymentManager;
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

        // Resolve gateway dynamically
        $gateway = $this->paymentManager->gateway($transaction->gateway->value);

        // Verify with gateway adapter
        $verification = $gateway->verifyPayment(array_merge($params, [
            'reference' => $transaction->reference,
            'amount' => (float) $transaction->amount,
        ]));

        if ($verification->success) {
            $transaction->gateway_payment_id = $verification->gatewayPaymentId;
            $transaction->gateway_signature = $params['razorpay_signature'] ?? $verification->meta['gateway_signature'] ?? null;
            $transaction->meta = array_merge($transaction->meta ?? [], ['frontend_callback' => $params]);
            $transaction->transitionTo(\App\Enums\Payment\PaymentStatus::AUTHORIZED);

            return true;
        } else {
            $this->markFailed->execute($transaction, $verification->message ?? 'Signature verification failed.');
            throw new PaymentVerificationException($verification->message ?? 'Signature verification failed.');
        }
    }
}
