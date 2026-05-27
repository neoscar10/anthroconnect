<?php

namespace App\DTOs\Payments;

class PaymentVerificationResult
{
    public bool $success;
    public ?string $reference;
    public ?string $gatewayPaymentId;
    public float $amount;
    public ?string $message;
    public array $meta;

    public function __construct(
        bool $success,
        ?string $reference,
        ?string $gatewayPaymentId,
        float $amount,
        ?string $message = null,
        array $meta = []
    ) {
        $this->success = $success;
        $this->reference = $reference;
        $this->gatewayPaymentId = $gatewayPaymentId;
        $this->amount = $amount;
        $this->message = $message;
        $this->meta = $meta;
    }
}
