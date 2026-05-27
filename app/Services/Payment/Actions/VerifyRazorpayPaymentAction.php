<?php

namespace App\Services\Payment\Actions;

use App\DTOs\Payments\PaymentVerificationResult;
use App\Models\PaymentTransaction;
use App\Services\Payment\Gateways\RazorpayGateway;

class VerifyRazorpayPaymentAction
{
    protected RazorpayGateway $gateway;

    public function __construct(RazorpayGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function execute(PaymentTransaction $transaction, array $params): PaymentVerificationResult
    {
        return $this->gateway->verifyPayment([
            'razorpay_order_id' => $params['razorpay_order_id'] ?? $transaction->gateway_order_id,
            'razorpay_payment_id' => $params['razorpay_payment_id'],
            'razorpay_signature' => $params['razorpay_signature'],
            'reference' => $transaction->reference,
            'amount' => (float) $transaction->amount,
        ]);
    }
}
