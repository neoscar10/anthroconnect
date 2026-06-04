<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\PaymentWebhookLog;
use App\Services\Payment\Gateways\CashfreeGateway;
use App\Jobs\Payments\ProcessCashfreeWebhookJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashfreeWebhookController extends Controller
{
    protected CashfreeGateway $gateway;

    public function __construct(CashfreeGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function handle(Request $request): JsonResponse
    {
        $payloadString = $request->getContent();
        
        // Retrieve signature from headers (x-cf-signature or standard x-signature)
        $signature = $request->header('x-cf-signature') ?? $request->header('x-signature');

        if (!$signature) {
            Log::warning('Cashfree Webhook: Missing signature header.');
            return response()->json(['error' => 'Missing signature'], 400);
        }

        // Verify Signature
        if (!$this->gateway->verifyWebhookSignature($payloadString, $signature)) {
            Log::warning('Cashfree Webhook: Signature verification failed.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $payload = json_decode($payloadString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Cashfree Webhook: Failed to parse payload JSON.');
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        // Cashfree webhook events structure
        $eventType = $payload['type'] ?? 'unknown';
        $orderId = $payload['data']['order']['order_id'] ?? null;
        $paymentId = $payload['data']['payment']['cf_payment_id'] ?? null;
        
        // Construct a unique event ID to guarantee idempotency / replay protection
        $eventId = 'cf_' . $orderId . '_' . $paymentId . '_' . $eventType;

        // Check if already processed
        $existingLog = PaymentWebhookLog::where('event_id', $eventId)->first();
        if ($existingLog) {
            Log::info("Cashfree Webhook: Event ID [{$eventId}] already processed.");
            return response()->json(['status' => 'already_processed']);
        }

        // Create log record
        $webhookLog = PaymentWebhookLog::create([
            'gateway' => 'cashfree',
            'event_type' => $eventType,
            'event_id' => $eventId,
            'signature' => $signature,
            'payload' => $payload,
            'processed' => false,
            'transaction_reference' => $orderId,
        ]);

        try {
            // Dispatch queue job for async processing
            ProcessCashfreeWebhookJob::dispatch($payload, $webhookLog->id);

            return response()->json(['status' => 'queued']);

        } catch (Exception $e) {
            Log::error('Cashfree Webhook dispatch crash', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);

            $webhookLog->update([
                'failure_reason' => 'Dispatch failed: ' . $e->getMessage(),
            ]);

            return response()->json(['error' => 'Dispatch failed'], 500);
        }
    }
}
