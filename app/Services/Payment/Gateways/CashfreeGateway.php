<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\DTOs\Payments\CreatePaymentResult;
use App\DTOs\Payments\PaymentVerificationResult;
use App\Services\Payment\Exceptions\CashfreeApiException;
use App\Services\Payment\Exceptions\CashfreeVerificationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CashfreeGateway implements PaymentGatewayInterface
{
    /**
     * Create a Cashfree Order.
     */
    public function createPayment(array $payload): CreatePaymentResult
    {
        $amount = (float) ($payload['amount'] ?? 0.0);
        $currency = $payload['currency'] ?? config('payments.currency', 'INR');
        $reference = $payload['reference'];
        $userId = $payload['user_id'] ?? 'guest';

        try {
            $creds = $this->getCredentials();
            $baseUrl = $this->getBaseUrl();

            // Fetch user info for billing details
            $user = \App\Models\User::find($userId);
            $customerName = $user ? $user->name : 'Customer';
            $customerEmail = $user ? $user->email : 'hello@example.com';
            $customerPhone = $user && $user->whatsapp_phone ? $user->whatsapp_phone : '9999999999';

            // Clean phone number (Cashfree expects standard format, usually min 10 digits)
            $customerPhone = preg_replace('/\D/', '', $customerPhone);
            if (strlen($customerPhone) < 10) {
                $customerPhone = '9999999999';
            }

            $response = Http::withHeaders([
                'x-api-version' => '2023-08-01',
                'x-client-id' => $creds['app_id'],
                'x-client-secret' => $creds['secret_key'],
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$baseUrl}/orders", [
                'order_id' => $reference,
                'order_amount' => $amount,
                'order_currency' => $currency,
                'customer_details' => [
                    'customer_id' => (string) $userId,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'customer_name' => $customerName,
                ],
                'order_meta' => [
                    'return_url' => route('dashboard'), // fallback redirect return url
                ]
            ]);

            if ($response->failed()) {
                throw new CashfreeApiException("Cashfree API returned error: " . $response->body());
            }

            $order = $response->json();
            $paymentSessionId = $order['payment_session_id'] ?? null;
            $paymentUrl = $order['payments']['payment_url'] ?? null;

            return new CreatePaymentResult(
                success: true,
                reference: $reference,
                amount: $amount,
                gatewayOrderId: $reference, // Cashfree uses order_id (which matches reference)
                gatewayPaymentId: null,
                meta: [
                    'gateway' => 'cashfree',
                    'order_id' => $reference,
                    'payment_session_id' => $paymentSessionId,
                    'payment_url' => $paymentUrl,
                ]
            );

        } catch (Exception $e) {
            Log::error('Cashfree order creation failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return new CreatePaymentResult(
                success: false,
                reference: $reference,
                amount: $amount,
                message: $e->getMessage()
            );
        }
    }

    /**
     * Verify payment status.
     */
    public function verifyPayment(array $payload): PaymentVerificationResult
    {
        $reference = $payload['reference'] ?? null;
        $amount = (float) ($payload['amount'] ?? 0.0);

        if (!$reference) {
            return new PaymentVerificationResult(
                success: false,
                reference: $reference,
                gatewayPaymentId: null,
                amount: $amount,
                message: 'Missing transaction reference.'
            );
        }

        try {
            $creds = $this->getCredentials();
            $baseUrl = $this->getBaseUrl();

            $response = Http::withHeaders([
                'x-api-version' => '2023-08-01',
                'x-client-id' => $creds['app_id'],
                'x-client-secret' => $creds['secret_key'],
                'Content-Type' => 'application/json',
            ])->timeout(10)->get("{$baseUrl}/orders/{$reference}");

            if ($response->failed()) {
                throw new CashfreeVerificationException("Cashfree verification call failed: " . $response->body());
            }

            $order = $response->json();
            $status = $order['order_status'] ?? 'PENDING';

            // Cashfree PAID state represents captured payment
            if ($status === 'PAID') {
                return new PaymentVerificationResult(
                    success: true,
                    reference: $reference,
                    gatewayPaymentId: $order['cf_order_id'] ?? null,
                    amount: (float) ($order['order_amount'] ?? $amount),
                    message: 'Payment verified successfully.'
                );
            }

            return new PaymentVerificationResult(
                success: false,
                reference: $reference,
                gatewayPaymentId: null,
                amount: $amount,
                message: "Order status is: {$status}"
            );

        } catch (Exception $e) {
            Log::error('Cashfree verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return new PaymentVerificationResult(
                success: false,
                reference: $reference,
                gatewayPaymentId: null,
                amount: $amount,
                message: $e->getMessage()
            );
        }
    }

    /**
     * Refund payment.
     */
    public function refundPayment(array $payload): array
    {
        $reference = $payload['gateway_order_id'] ?? $payload['reference'] ?? null;
        $amount = $payload['amount'] ?? null;

        if (!$reference) {
            throw new CashfreeApiException('Refund failed: gateway_order_id/reference is required.');
        }

        try {
            $creds = $this->getCredentials();
            $baseUrl = $this->getBaseUrl();
            $refundId = 'REF-' . time() . '-' . rand(1000, 9999);

            $body = [
                'refund_amount' => (float) $amount,
                'refund_id' => $refundId,
                'refund_note' => $payload['reason'] ?? 'Membership Refund',
            ];

            $response = Http::withHeaders([
                'x-api-version' => '2023-08-01',
                'x-client-id' => $creds['app_id'],
                'x-client-secret' => $creds['secret_key'],
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$baseUrl}/orders/{$reference}/refunds", $body);

            if ($response->failed()) {
                throw new CashfreeApiException("Cashfree refund call failed: " . $response->body());
            }

            $refund = $response->json();

            return [
                'success' => true,
                'refund_id' => $refund['cf_refund_id'] ?? $refundId,
                'amount' => (float) ($refund['refund_amount'] ?? $amount),
            ];

        } catch (Exception $e) {
            Log::error('Cashfree refund failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
            throw new CashfreeApiException('Cashfree refund error: ' . $e->getMessage());
        }
    }

    /**
     * Verify webhook payload signature.
     */
    public function verifyWebhookSignature(string $payload, string $signatureHeader): bool
    {
        $creds = $this->getCredentials();
        $secretKey = $creds['secret_key'];

        if (empty($secretKey)) {
            Log::error('Cashfree secret key is not configured.');
            return false;
        }

        try {
            $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secretKey, true));
            return hash_equals($expectedSignature, $signatureHeader);
        } catch (Exception $e) {
            Log::error('Cashfree webhook signature verification process crashed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getGatewayName(): string
    {
        return 'cashfree';
    }

    protected function getCredentials(): array
    {
        $mode = config('payments.mode', 'test');
        $config = config("payments.gateways.cashfree.{$mode}", []);

        return [
            'app_id' => $config['app_id'] ?? '',
            'secret_key' => $config['secret_key'] ?? '',
        ];
    }

    protected function getBaseUrl(): string
    {
        $mode = config('payments.mode', 'test');
        return $mode === 'live' 
            ? 'https://api.cashfree.com/pg' 
            : 'https://sandbox.cashfree.com/pg';
    }
}
