<?php

namespace App\Services\Payment\Actions;

use App\Models\PaymentTransaction;
use App\Enums\Payment\PaymentStatus;
use Illuminate\Support\Facades\Log;

class ProcessCashfreeWebhookAction
{
    protected CapturePaymentAction $capturePayment;
    protected HandleCapturedPaymentAction $handleCaptured;
    protected MarkTransactionFailedAction $markFailed;

    public function __construct(
        CapturePaymentAction $capturePayment,
        HandleCapturedPaymentAction $handleCaptured,
        MarkTransactionFailedAction $markFailed
    ) {
        $this->capturePayment = $capturePayment;
        $this->handleCaptured = $handleCaptured;
        $this->markFailed = $markFailed;
    }

    public function execute(array $payload): bool
    {
        $event = $payload['type'] ?? null;
        if (!$event) {
            Log::warning('Cashfree webhook payload missing event type.');
            return false;
        }

        $orderData = $payload['data']['order'] ?? null;
        if (!$orderData) {
            Log::warning('Cashfree webhook payload missing order data.');
            return false;
        }

        $orderId = $orderData['order_id'] ?? null;
        $transaction = PaymentTransaction::where('gateway_order_id', $orderId)->first();

        if (!$transaction) {
            Log::warning("No matching transaction found for Cashfree order ID [{$orderId}].");
            return false;
        }

        Log::info("Processing Cashfree webhook event [{$event}] for transaction [{$transaction->reference}].");

        switch ($event) {
            case 'PAYMENT_SUCCESS_WEBHOOK':
                $paymentData = $payload['data']['payment'] ?? [];
                $paymentId = $paymentData['cf_payment_id'] ?? null;
                
                $this->capturePayment->execute($transaction, (string) $paymentId, null, [
                    'webhook_payload' => $payload,
                ]);
                
                $this->handleCaptured->execute($transaction);
                break;

            case 'PAYMENT_FAILED_WEBHOOK':
                $paymentData = $payload['data']['payment'] ?? [];
                $reason = $paymentData['payment_message'] ?? 'Payment failed at gateway.';
                
                $this->markFailed->execute($transaction, $reason, [
                    'webhook_payload' => $payload,
                ]);
                break;

            case 'REFUND_SUCCESS_WEBHOOK':
                $transaction->update([
                    'status' => PaymentStatus::REFUNDED,
                    'refunded_at' => now(),
                    'meta' => array_merge($transaction->meta ?? [], ['refund_webhook_payload' => $payload]),
                ]);
                break;

            default:
                Log::info("Unhandled Cashfree webhook event [{$event}] ignored.");
                break;
        }

        return true;
    }
}
