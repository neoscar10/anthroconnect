<?php

namespace App\Jobs\Payments;

use App\Models\PaymentWebhookLog;
use App\Services\Payment\Actions\ProcessRazorpayWebhookAction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRazorpayWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 30;

    protected array $payload;
    protected int $logId;

    public function __construct(array $payload, int $logId)
    {
        $this->payload = $payload;
        $this->logId = $logId;
    }

    public function handle(ProcessRazorpayWebhookAction $action): void
    {
        $logModel = PaymentWebhookLog::find($this->logId);
        
        if ($logModel) {
            $logModel->increment('retry_count');
        }

        $event = $this->payload['event'] ?? 'unknown';
        Log::channel('payments')->info("Processing queued Razorpay Webhook Job: Log ID [{$this->logId}], Event [{$event}]");

        try {
            $success = $action->execute($this->payload);

            if ($logModel) {
                if ($success) {
                    $logModel->update([
                        'processed' => true,
                        'processed_at' => now(),
                        'failure_reason' => null,
                        'exception_trace' => null,
                    ]);
                } else {
                    $logModel->update([
                        'failure_reason' => 'Processing returned false (no match or ignored event).',
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::channel('payments')->error("Queue Job Exception on Webhook processing log ID [{$this->logId}]: " . $e->getMessage());

            if ($logModel) {
                $logModel->update([
                    'failure_reason' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ]);
            }

            throw $e; // Re-throw to trigger Laravel queue retry
        }
    }
}
