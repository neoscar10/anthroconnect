<?php

namespace App\DTOs\Payments;

class CreatePaymentResult
{
    public bool $success;
    public ?string $reference;
    public float $amount;
    public ?string $gatewayOrderId;
    public ?string $gatewayPaymentId;
    public array $meta;
    public ?string $message;

    public function __construct(
        bool $success,
        ?string $reference,
        float $amount,
        ?string $gatewayOrderId = null,
        ?string $gatewayPaymentId = null,
        array $meta = [],
        ?string $message = null
    ) {
        $this->success = $success;
        $this->reference = $reference;
        $this->amount = $amount;
        $this->gatewayOrderId = $gatewayOrderId;
        $this->gatewayPaymentId = $gatewayPaymentId;
        $this->meta = $meta;
        $this->message = $message;
    }
}
