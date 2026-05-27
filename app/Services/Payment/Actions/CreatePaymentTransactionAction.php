<?php

namespace App\Services\Payment\Actions;

use App\DTOs\Payments\PaymentTransactionData;
use App\Enums\Payment\PaymentStatus;
use App\Models\PaymentTransaction;

class CreatePaymentTransactionAction
{
    public function execute(PaymentTransactionData $data): PaymentTransaction
    {
        return PaymentTransaction::create([
            'user_id' => $data->userId,
            'amount' => $data->amount,
            'currency' => $data->currency,
            'gateway' => $data->gateway,
            'purpose' => $data->purpose,
            'reference' => $data->reference,
            'status' => PaymentStatus::INITIATED,
            'meta' => $data->meta,
        ]);
    }
}
