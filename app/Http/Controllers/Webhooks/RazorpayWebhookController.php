<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\PaymentWebhookLog;
use App\Services\Payment\Actions\ProcessRazorpayWebhookAction;
use App\Services\Payment\Gateways\RazorpayGateway;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Jobs\Payments\ProcessRazorpayWebhookJob;

class RazorpayWebhookController extends Controller
{
    protected RazorpayGateway $gateway;

    public function __construct(RazorpayGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function handle(Request $request): JsonResponse
    {
        $payloadString = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        if (!$signature) {
            Log::warning('Razorpay Webhook: Missing X-Razorpay-Signature header.');
            return response()->json(['error' => 'Missing signature'], 400);
        }

        // Verify Signature
        if (!$this->gateway->verifyWebhookSignature($payloadString, $signature)) {
            Log::warning('Razorpay Webhook: Signature verification failed.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $payload = json_decode($payloadString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Razorpay Webhook: Failed to parse payload JSON.');
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        $eventId = $payload['id'] ?? null;
        $eventType = $payload['event'] ?? 'unknown';

        // Check if webhook event has already been logged/processed
        $existingLog = PaymentWebhookLog::where('event_id', $eventId)->first();
        if ($existingLog) {
            Log::info("Razorpay Webhook: Event ID [{$eventId}] already processed.");
            return response()->json(['status' => 'already_processed']);
        }

        // Create log record
        $webhookLog = PaymentWebhookLog::create([
            'gateway' => 'razorpay',
            'event_type' => $eventType,
            'event_id' => $eventId,
            'signature' => $signature,
            'payload' => $payload,
            'processed' => false,
        ]);

        try {
            // Dispatch queued job for asynchronous processing
            ProcessRazorpayWebhookJob::dispatch($payload, $webhookLog->id);

            return response()->json(['status' => 'queued']);

        } catch (Exception $e) {
            Log::error('Razorpay Webhook dispatch crash', [
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
