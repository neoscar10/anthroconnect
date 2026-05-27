<?php

namespace Tests\Feature\Payment;

use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentPurpose;
use App\Enums\Payment\PaymentStatus;
use App\Jobs\Payments\ProcessRazorpayWebhookJob;
use App\Models\MembershipSetting;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhookLog;
use App\Models\User;
use App\Services\Payment\Gateways\RazorpayGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class RazorpayReliabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_webhook_dispatches_queued_job(): void
    {
        Queue::fake();

        $gatewayMock = Mockery::mock(RazorpayGateway::class);
        $gatewayMock->shouldReceive('verifyWebhookSignature')->andReturn(true);
        $this->app->instance(RazorpayGateway::class, $gatewayMock);

        $response = $this->postJson('/webhooks/razorpay', [
            'id' => 'evt_999',
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_123',
                        'order_id' => 'order_123',
                    ]
                ]
            ]
        ], [
            'X-Razorpay-Signature' => 'valid_sig',
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'queued']);

        $log = PaymentWebhookLog::where('event_id', 'evt_999')->first();
        $this->assertNotNull($log);
        $this->assertFalse($log->processed);

        Queue::assertPushed(ProcessRazorpayWebhookJob::class, function ($job) use ($log) {
            $refJob = new \ReflectionClass($job);
            $logIdProp = $refJob->getProperty('logId');
            $logIdProp->setAccessible(true);
            return $logIdProp->getValue($job) === $log->id;
        });
    }

    public function test_webhook_job_executes_successfully(): void
    {
        $user = User::factory()->create();
        $setting = MembershipSetting::create([
            'title' => 'Premium Pass',
            'price_inr' => 499.00,
            'description' => 'Unlimited access',
            'is_active' => true,
        ]);

        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'amount' => 499.00,
            'currency' => 'INR',
            'gateway' => PaymentGateway::RAZORPAY,
            'purpose' => PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'ACMEM-RELIABILITY',
            'gateway_order_id' => 'order_rzp_999',
            'status' => PaymentStatus::PENDING,
            'meta' => ['membership_setting_id' => $setting->id],
        ]);

        $log = PaymentWebhookLog::create([
            'gateway' => 'razorpay',
            'event_type' => 'payment.captured',
            'event_id' => 'evt_123',
            'payload' => [
                'event' => 'payment.captured',
                'payload' => [
                    'payment' => [
                        'entity' => [
                            'id' => 'pay_rzp_111',
                            'order_id' => 'order_rzp_999',
                        ]
                    ]
                ]
            ],
            'signature' => 'sig_hash',
            'processed' => false,
        ]);

        $job = new ProcessRazorpayWebhookJob($log->payload, $log->id);
        app()->call([$job, 'handle']);

        $transaction->refresh();
        $this->assertSame(PaymentStatus::CAPTURED, $transaction->status);
        $this->assertSame('pay_rzp_111', $transaction->gateway_payment_id);
        $this->assertTrue($user->fresh()->isMember());

        $log->refresh();
        $this->assertTrue($log->processed);
        $this->assertSame(1, $log->retry_count);
    }

    public function test_webhook_idempotency_prevents_duplicate_processing(): void
    {
        $user = User::factory()->create();
        $setting = MembershipSetting::create([
            'title' => 'Premium Pass',
            'price_inr' => 499.00,
            'description' => 'Unlimited access',
            'is_active' => true,
        ]);

        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'amount' => 499.00,
            'currency' => 'INR',
            'gateway' => PaymentGateway::RAZORPAY,
            'purpose' => PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'ACMEM-IDEMPOTENCY',
            'gateway_order_id' => 'order_rzp_999',
            'status' => PaymentStatus::CAPTURED,
            'meta' => ['membership_setting_id' => $setting->id],
        ]);

        // Setup active membership
        $user->membership()->create([
            'membership_setting_id' => $setting->id,
            'amount_paid_inr' => 499.00,
            'status' => 'active',
            'started_at' => now(),
            'payment_reference' => 'ACMEM-IDEMPOTENCY',
        ]);

        $log = PaymentWebhookLog::create([
            'gateway' => 'razorpay',
            'event_type' => 'payment.captured',
            'event_id' => 'evt_123',
            'payload' => [
                'event' => 'payment.captured',
                'payload' => [
                    'payment' => [
                        'entity' => [
                            'id' => 'pay_rzp_111',
                            'order_id' => 'order_rzp_999',
                        ]
                    ]
                ]
            ],
            'signature' => 'sig_hash',
            'processed' => false,
        ]);

        $job = new ProcessRazorpayWebhookJob($log->payload, $log->id);
        app()->call([$job, 'handle']);

        $log->refresh();
        $this->assertTrue($log->processed);
        $this->assertSame(1, $user->fresh()->membership()->count());
    }

    public function test_invalid_status_regressions_prevented(): void
    {
        $user = User::factory()->create();
        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'currency' => 'INR',
            'gateway' => PaymentGateway::RAZORPAY,
            'purpose' => PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'TEST-REGRESSION',
            'status' => PaymentStatus::CAPTURED,
        ]);

        $this->expectException(\Exception::class);
        $transaction->transitionTo(PaymentStatus::PENDING);
    }

    public function test_admin_payment_visibility_gates(): void
    {
        $this->get('/admin/payments')->assertRedirect('/login');

        $user = User::factory()->create();
        $admin = User::factory()->create();
        \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
        $admin->assignRole('Admin');

        $this->actingAs($user)->get('/admin/payments')->assertStatus(403);
        $this->actingAs($admin)->get('/admin/payments')->assertOk();
    }
}
