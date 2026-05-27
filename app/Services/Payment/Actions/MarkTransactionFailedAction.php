<?php

namespace App\Services\Payment\Actions;

use App\Enums\Payment\PaymentStatus;
use App\Models\PaymentTransaction;

class MarkTransactionFailedAction
{
    public function execute(
        PaymentTransaction $transaction,
        string $reason,
        array $additionalMeta = []
    ): PaymentTransaction {
        $meta = array_merge($transaction->meta ?? [], $additionalMeta);

        $transaction->failure_reason = $reason;
        $transaction->meta = $meta;
        $transaction->transitionTo(PaymentStatus::FAILED);

        return $transaction;
    }
}
