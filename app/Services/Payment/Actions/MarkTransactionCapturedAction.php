<?php

namespace App\Services\Payment\Actions;

use App\Enums\Payment\PaymentStatus;
use App\Models\PaymentTransaction;

class MarkTransactionCapturedAction
{
    public function execute(
        PaymentTransaction $transaction,
        ?string $gatewayPaymentId = null,
        ?string $gatewaySignature = null,
        array $additionalMeta = []
    ): PaymentTransaction {
        $meta = array_merge($transaction->meta ?? [], $additionalMeta);

        $transaction->gateway_payment_id = $gatewayPaymentId ?? $transaction->gateway_payment_id;
        $transaction->gateway_signature = $gatewaySignature ?? $transaction->gateway_signature;
        $transaction->meta = $meta;
        $transaction->transitionTo(PaymentStatus::CAPTURED);

        return $transaction;
    }
}
