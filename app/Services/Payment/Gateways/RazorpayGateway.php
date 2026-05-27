<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\DTOs\Payments\CreatePaymentResult;
use App\DTOs\Payments\PaymentVerificationResult;
use App\Services\Payment\Exceptions\PaymentException;
use Exception;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayGateway implements PaymentGatewayInterface
{
    /**
     * Create a Razorpay Order.
     */
    public function createPayment(array $payload): CreatePaymentResult
    {
        $amount = (float) ($payload['amount'] ?? 0.0);
        $currency = $payload['currency'] ?? config('payments.currency', 'INR');
        $reference = $payload['reference'];

        // Convert to Paise
        $amountInPaise = (int) round($amount * 100);

        try {
            $creds = $this->getCredentials();
            $api = new Api($creds['key_id'], $creds['key_secret']);

            $order = $api->order->create([
                'receipt' => $reference,
                'amount' => $amountInPaise,
                'currency' => $currency,
                'notes' => [
                    'user_id' => $payload['user_id'] ?? null,
                    'purpose' => $payload['purpose'] ?? null,
                    'membership_setting_id' => $payload['membership_setting_id'] ?? null,
                ],
            ]);

            return new CreatePaymentResult(
                success: true,
                reference: $reference,
                amount: $amount,
                gatewayOrderId: $order['id'],
                gatewayPaymentId: null,
                meta: [
                    'gateway' => 'razorpay',
                    'order_id' => $order['id'],
                    'receipt' => $order['receipt'],
                    'amount_paise' => $order['amount'],
                ]
            );

        } catch (Exception $e) {
            Log::error('Razorpay order creation failed', [
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
     * Verify payment signature.
     */
    public function verifyPayment(array $payload): PaymentVerificationResult
    {
        $orderId = $payload['razorpay_order_id'] ?? null;
        $paymentId = $payload['razorpay_payment_id'] ?? null;
        $signature = $payload['razorpay_signature'] ?? null;
        $reference = $payload['reference'] ?? null;
        $amount = (float) ($payload['amount'] ?? 0.0);

        if (!$orderId || !$paymentId || !$signature) {
            return new PaymentVerificationResult(
                success: false,
                reference: $reference,
                gatewayPaymentId: $paymentId,
                amount: $amount,
                message: 'Missing verification parameters.'
            );
        }

        try {
            $creds = $this->getCredentials();
            $api = new Api($creds['key_id'], $creds['key_secret']);

            // Verify signature using the SDK helper method
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);

            return new PaymentVerificationResult(
                success: true,
                reference: $reference,
                gatewayPaymentId: $paymentId,
                amount: $amount,
                message: 'Signature verified successfully.'
            );

        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay signature verification failed', [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return new PaymentVerificationResult(
                success: false,
                reference: $reference,
                gatewayPaymentId: $paymentId,
                amount: $amount,
                message: 'Signature verification failed: ' . $e->getMessage()
            );
        } catch (Exception $e) {
            Log::error('Razorpay verification error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return new PaymentVerificationResult(
                success: false,
                reference: $reference,
                gatewayPaymentId: $paymentId,
                amount: $amount,
                message: 'Verification process encountered an error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Verify webhook payload signature.
     */
    public function verifyWebhookSignature(string $payload, string $signatureHeader): bool
    {
        $creds = $this->getCredentials();
        $webhookSecret = $creds['webhook_secret'];

        if (empty($webhookSecret)) {
            Log::error('Razorpay webhook secret is not configured.');
            return false;
        }

        try {
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            return hash_equals($expectedSignature, $signatureHeader);
        } catch (Exception $e) {
            Log::error('Razorpay webhook signature verification process crashed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Refund payment.
     */
    public function refundPayment(array $payload): array
    {
        $paymentId = $payload['gateway_payment_id'] ?? null;
        $amount = $payload['amount'] ?? null; // in INR

        if (!$paymentId) {
            throw new PaymentException('Refund failed: gateway_payment_id is required.');
        }

        try {
            $creds = $this->getCredentials();
            $api = new Api($creds['key_id'], $creds['key_secret']);

            $refundPayload = [];
            if ($amount) {
                $refundPayload['amount'] = (int) round($amount * 100);
            }

            $refund = $api->payment->fetch($paymentId)->refund($refundPayload);

            return [
                'success' => true,
                'refund_id' => $refund['id'],
                'amount' => (float) ($refund['amount'] / 100),
            ];

        } catch (Exception $e) {
            Log::error('Razorpay refund request failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            throw new PaymentException('Razorpay refund error: ' . $e->getMessage());
        }
    }

    public function getGatewayName(): string
    {
        return 'razorpay';
    }

    protected function getCredentials(): array
    {
        $mode = config('payments.mode', 'test');
        $config = config("payments.gateways.razorpay.{$mode}", []);

        return [
            'key_id' => $config['key_id'] ?? '',
            'key_secret' => $config['key_secret'] ?? '',
            'webhook_secret' => $config['webhook_secret'] ?? '',
        ];
    }
}
