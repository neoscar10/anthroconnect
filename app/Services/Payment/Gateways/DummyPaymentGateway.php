<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\DTOs\Payments\CreatePaymentResult;
use App\DTOs\Payments\PaymentVerificationResult;
use Illuminate\Support\Str;

class DummyPaymentGateway implements PaymentGatewayInterface
{
    /**
     * Create a payment checkout session / order representation.
     */
    public function createPayment(array $payload): CreatePaymentResult
    {
        $cardData = $payload['card_data'] ?? [];
        $amount = (float) ($payload['amount'] ?? 0.0);

        $cardNumber = preg_replace('/\D/', '', $cardData['number'] ?? '');
        
        // Simulate a tiny delay to feel realistic
        usleep(800000); // 800ms
        
        if (strlen($cardNumber) < 12) {
            return new CreatePaymentResult(
                success: false,
                reference: null,
                amount: $amount,
                message: 'Invalid card number format.',
                meta: []
            );
        }



        $reference = $payload['reference'] ?? ('ACMEM-' . date('Ymd') . '-' . strtoupper(Str::random(6)));

        $maskedLast4 = substr($cardNumber, -4);
        $brand = $this->guessBrand($cardNumber);

        return new CreatePaymentResult(
            success: true,
            reference: $reference,
            amount: $amount,
            gatewayOrderId: 'dummy_order_' . Str::random(10),
            gatewayPaymentId: 'dummy_pay_' . Str::random(10),
            meta: [
                'masked_last4' => $maskedLast4,
                'brand' => $brand,
            ],
            message: 'Payment successful!'
        );
    }

    /**
     * Verify the payment from the webhook or redirect callback.
     */
    public function verifyPayment(array $payload): PaymentVerificationResult
    {
        $success = (bool) ($payload['success'] ?? false);
        $reference = $payload['reference'] ?? null;
        $gatewayPaymentId = $payload['gateway_payment_id'] ?? null;
        $amount = (float) ($payload['amount'] ?? 0.0);
        $message = $payload['message'] ?? ($success ? 'Payment verified successfully.' : 'Payment verification failed.');
        $meta = $payload['meta'] ?? [];

        return new PaymentVerificationResult(
            success: $success,
            reference: $reference,
            gatewayPaymentId: $gatewayPaymentId,
            amount: $amount,
            message: $message,
            meta: $meta
        );
    }

    /**
     * Refund a processed payment.
     */
    public function refundPayment(array $payload): array
    {
        return [
            'success' => true,
            'refund_id' => 'dummy_ref_' . Str::random(10),
            'amount' => (float) ($payload['amount'] ?? 0.0),
        ];
    }

    /**
     * Get the name of this gateway adapter.
     */
    public function getGatewayName(): string
    {
        return 'dummy';
    }

    /**
     * Guess card brand for metadata (dummy logic).
     */
    protected function guessBrand(string $number): string
    {
        if (str_starts_with($number, '4')) return 'Visa';
        if (str_starts_with($number, '5')) return 'Mastercard';
        if (str_starts_with($number, '3')) return 'Amex';
        return 'Standard Card';
    }
}
