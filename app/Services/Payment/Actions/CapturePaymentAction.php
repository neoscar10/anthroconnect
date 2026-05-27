<?php

namespace App\Services\Payment\Actions;

use App\Models\PaymentTransaction;
use Exception;

class CapturePaymentAction
{
    protected MarkTransactionCapturedAction $markCapturedAction;

    public function __construct(MarkTransactionCapturedAction $markCapturedAction)
    {
        $this->markCapturedAction = $markCapturedAction;
    }

    public function execute(
        PaymentTransaction $transaction,
        ?string $gatewayPaymentId = null,
        ?string $gatewaySignature = null,
        array $additionalMeta = []
    ): PaymentTransaction {
        if ($transaction->isCaptured()) {
            // Already captured - idempotent return
            return $transaction;
        }

        if (!$transaction->canBeCaptured()) {
            throw new Exception("Transaction in status [{$transaction->status->value}] cannot be transitioned to captured.");
        }

        return $this->markCapturedAction->execute(
            transaction: $transaction,
            gatewayPaymentId: $gatewayPaymentId,
            gatewaySignature: $gatewaySignature,
            additionalMeta: $additionalMeta
        );
    }
}
