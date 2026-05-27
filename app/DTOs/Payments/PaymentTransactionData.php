<?php

namespace App\DTOs\Payments;

use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentPurpose;

class PaymentTransactionData
{
    public int $userId;
    public float $amount;
    public string $currency;
    public PaymentGateway $gateway;
    public PaymentPurpose $purpose;
    public string $reference;
    public array $meta;

    public function __construct(
        int $userId,
        float $amount,
        string $currency,
        PaymentGateway $gateway,
        PaymentPurpose $purpose,
        string $reference,
        array $meta = []
    ) {
        $this->userId = $userId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->gateway = $gateway;
        $this->purpose = $purpose;
        $this->reference = $reference;
        $this->meta = $meta;
    }
}
