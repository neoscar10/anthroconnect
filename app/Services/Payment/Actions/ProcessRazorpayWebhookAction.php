<?php

namespace App\Services\Payment\Actions;

use App\Models\PaymentTransaction;
use App\Enums\Payment\PaymentStatus;
use Illuminate\Support\Facades\Log;

class ProcessRazorpayWebhookAction
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
        $event = $payload['event'] ?? null;
        if (!$event) {
            Log::warning('Razorpay webhook payload missing event type.');
            return false;
        }

        $entity = $payload['payload']['payment']['entity'] ?? null;
        if (!$entity) {
            $entity = $payload['payload']['refund']['entity'] ?? null;
        }

        if (!$entity) {
            Log::warning('Razorpay webhook payload missing entity data.');
            return false;
        }

        $orderId = $entity['order_id'] ?? null;
        if (!$orderId && $event === 'refund.processed') {
            $paymentId = $entity['payment_id'] ?? null;
            $transaction = PaymentTransaction::where('gateway_payment_id', $paymentId)->first();
        } else {
            $transaction = PaymentTransaction::where('gateway_order_id', $orderId)->first();
        }

        if (!$transaction) {
            Log::warning("No matching transaction found for Razorpay order ID [{$orderId}].");
            return false;
        }

        Log::info("Processing Razorpay webhook event [{$event}] for transaction [{$transaction->reference}].");

        switch ($event) {
            case 'payment.authorized':
                if ($transaction->status === PaymentStatus::INITIATED) {
                    $transaction->update(['status' => PaymentStatus::PENDING]);
                }
                break;

            case 'payment.captured':
                $paymentId = $entity['id'] ?? null;
                $this->capturePayment->execute($transaction, $paymentId, null, [
                    'webhook_payload' => $payload,
                ]);
                $this->handleCaptured->execute($transaction);
                break;

            case 'payment.failed':
                $reason = $entity['error_description'] ?? 'Payment failed at gateway.';
                $this->markFailed->execute($transaction, $reason, [
                    'webhook_payload' => $payload,
                ]);
                break;

            case 'refund.processed':
                $transaction->update([
                    'status' => PaymentStatus::REFUNDED,
                    'refunded_at' => now(),
                    'meta' => array_merge($transaction->meta ?? [], ['refund_webhook_payload' => $payload]),
                ]);
                break;

            default:
                Log::info("Unhandled Razorpay webhook event [{$event}] ignored.");
                break;
        }

        return true;
    }
}
