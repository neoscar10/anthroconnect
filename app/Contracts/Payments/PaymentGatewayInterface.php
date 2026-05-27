<?php

namespace App\Contracts\Payments;

use App\DTOs\Payments\CreatePaymentResult;
use App\DTOs\Payments\PaymentVerificationResult;

interface PaymentGatewayInterface
{
    public function createPayment(array $payload): CreatePaymentResult;

    public function verifyPayment(array $payload): PaymentVerificationResult;

    public function refundPayment(array $payload): array;

    public function getGatewayName(): string;
}
