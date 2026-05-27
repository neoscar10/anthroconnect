<?php

namespace Tests\Feature\Payment;

use App\DTOs\Payments\CreatePaymentResult;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentPurpose;
use App\Enums\Payment\PaymentStatus;
use App\Models\MembershipSetting;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhookLog;
use App\Models\User;
use App\Services\Payment\Gateways\RazorpayGateway;
use App\Services\Payment\PaymentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class RazorpayIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_razorpay_order_creation_transitions_to_pending(): void
    {
        config(['payments.default_gateway' => 'razorpay']);

        $gatewayMock = Mockery::mock(RazorpayGateway::class);
        $gatewayMock->shouldReceive('getGatewayName')->andReturn('razorpay');
        $gatewayMock->shouldReceive('createPayment')
            ->once()
            ->andReturn(new CreatePaymentResult(
                success: true,
                reference: 'ACMEM-ORDER-123',
                amount: 499.00,
                gatewayOrderId: 'order_rzp_999',
                meta: ['order_id' => 'order_rzp_999']
            ));

        $this->app->instance(RazorpayGateway::class, $gatewayMock);

        $user = User::factory()->create();
        $setting = MembershipSetting::create([
            'title' => 'Premium Pass',
            'price_inr' => 499.00,
            'description' => 'Unlimited access',
            'is_active' => true,
        ]);

        $manager = app(PaymentManager::class);
        $result = $manager->purchaseMembership($user, 499.00, [], $setting->id);

        $this->assertTrue($result->success);
        $this->assertSame('order_rzp_999', $result->gatewayOrderId);

        // Verify transaction is pending and has order id
        $transaction = PaymentTransaction::where('gateway_order_id', 'order_rzp_999')->first();
        $this->assertNotNull($transaction);
        $this->assertSame(PaymentStatus::PENDING, $transaction->status);
        $this->assertSame(PaymentGateway::RAZORPAY, $transaction->gateway);

        // Verify membership NOT activated yet
        $this->assertFalse($user->fresh()->isMember());
    }

    public function test_webhook_payment_captured_activates_membership(): void
    {
        $user = User::factory()->create();
        $setting = MembershipSetting::create([
            'title' => 'Premium Pass',
            'price_inr' => 499.00,
            'description' => 'Unlimited access',
            'is_active' => true,
        ]);

        // Setup a pending transaction in DB
        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'amount' => 499.00,
            'currency' => 'INR',
            'gateway' => PaymentGateway::RAZORPAY,
            'purpose' => PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'ACMEM-TEST-WEBHOOK',
            'gateway_order_id' => 'order_rzp_999',
            'status' => PaymentStatus::PENDING,
            'meta' => ['membership_setting_id' => $setting->id],
        ]);

        // Mock signature verification
        $gatewayMock = Mockery::mock(RazorpayGateway::class);
        $gatewayMock->shouldReceive('verifyWebhookSignature')->andReturn(true);
        $this->app->instance(RazorpayGateway::class, $gatewayMock);

        $webhookPayload = [
            'id' => 'evt_123',
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_rzp_111',
                        'order_id' => 'order_rzp_999',
                        'amount' => 49900,
                        'currency' => 'INR',
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/webhooks/razorpay', $webhookPayload, [
            'X-Razorpay-Signature' => 'fake_signature_hash',
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'queued']);

        $log = PaymentWebhookLog::where('event_id', 'evt_123')->first();
        $this->assertNotNull($log);

        $job = new \App\Jobs\Payments\ProcessRazorpayWebhookJob($log->payload, $log->id);
        app()->call([$job, 'handle']);

        // Assert transaction status updated to CAPTURED
        $transaction->refresh();
        $this->assertSame(PaymentStatus::CAPTURED, $transaction->status);
        $this->assertSame('pay_rzp_111', $transaction->gateway_payment_id);

        // Assert Webhook log is captured
        $this->assertDatabaseHas('payment_webhook_logs', [
            'event_id' => 'evt_123',
            'processed' => true,
        ]);

        // Assert membership activated
        $this->assertTrue($user->fresh()->isMember());
    }

    public function test_duplicate_webhook_is_ignored(): void
    {
        // Add existing log
        PaymentWebhookLog::create([
            'gateway' => 'razorpay',
            'event_type' => 'payment.captured',
            'event_id' => 'evt_123',
            'payload' => [],
            'signature' => 'some_sig',
            'processed' => true,
        ]);

        // Mock signature verification
        $gatewayMock = Mockery::mock(RazorpayGateway::class);
        $gatewayMock->shouldReceive('verifyWebhookSignature')->andReturn(true);
        $this->app->instance(RazorpayGateway::class, $gatewayMock);

        $response = $this->postJson('/webhooks/razorpay', [
            'id' => 'evt_123',
            'event' => 'payment.captured',
        ], [
            'X-Razorpay-Signature' => 'some_sig',
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'already_processed']);
    }

    public function test_invalid_signature_webhook_is_rejected(): void
    {
        // Mock signature verification failing
        $gatewayMock = Mockery::mock(RazorpayGateway::class);
        $gatewayMock->shouldReceive('verifyWebhookSignature')->andReturn(false);
        $this->app->instance(RazorpayGateway::class, $gatewayMock);

        $response = $this->postJson('/webhooks/razorpay', [
            'id' => 'evt_123',
            'event' => 'payment.captured',
        ], [
            'X-Razorpay-Signature' => 'invalid_sig',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid signature']);
    }
}
