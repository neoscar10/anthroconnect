<?php

namespace App\Services\Payment;

use Illuminate\Support\Str;

class DummyPaymentGateway
{
    /**
     * Simulate a card payment.
     * 
     * @param array $cardData Contains name, number, expiry, cvv
     * @param float $amount The amount to charge in INR
     * @return array Response with success state and transaction reference
     */
    public function process(array $cardData, float $amount): array
    {
        // Simple simulation: always succeed if card number is roughly valid length
        $cardNumber = preg_replace('/\D/', '', $cardData['number'] ?? '');
        
        // Simulate a tiny delay to feel realistic
        usleep(800000); // 800ms
        
        if (strlen($cardNumber) < 12) {
            return [
                'success' => false,
                'message' => 'Invalid card number format.',
                'reference' => null,
            ];
        }

        // Generate a realistic dummy reference
        $reference = 'ACMEM-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        return [
            'success' => true,
            'message' => 'Payment successful!',
            'reference' => $reference,
            'amount' => $amount,
            'masked_last4' => substr($cardNumber, -4),
            'brand' => $this->guessBrand($cardNumber),
        ];
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
