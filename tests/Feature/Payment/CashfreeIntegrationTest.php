<?php

namespace Tests\Feature\Payment;

use App\DTOs\Payments\CreatePaymentResult;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentPurpose;
use App\Enums\Payment\PaymentStatus;
use App\Models\MembershipSetting;
use App\Models\PaymentSetting;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhookLog;
use App\Models\User;
use App\Services\Payment\Gateways\CashfreeGateway;
use App\Services\Payment\PaymentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CashfreeIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles/permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Seed settings
        PaymentSetting::query()->delete();
        PaymentSetting::create([
            'gateway' => 'cashfree',
            'display_name' => 'Cashfree',
            'is_enabled' => true,
            'is_default' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_cashfree_order_creation_transitions_to_pending(): void
    {
        $user = User::factory()->create();
        $setting = MembershipSetting::create([
            'title' => 'Premium Pass',
            'price_inr' => 499.00,
            'description' => 'Unlimited access',
            'is_active' => true,
        ]);

        // Mock Cashfree Orders API
        Http::fake([
            'https://sandbox.cashfree.com/pg/orders' => Http::response([
                'payment_session_id' => 'session_cf_123',
                'payments' => [
                    'payment_url' => 'https://checkout.cashfree.com/session_cf_123'
                ],
                'cf_order_id' => '111222'
            ], 200)
        ]);

        $manager = app(PaymentManager::class);
        $result = $manager->purchaseMembership($user, 499.00, [], $setting->id);

        $this->assertTrue($result->success);
        $this->assertSame($result->gatewayOrderId, $result->reference); // Cashfree returns reference as order_id

        $transaction = PaymentTransaction::where('gateway_order_id', $result->gatewayOrderId)->first();
        $this->assertNotNull($transaction);
        $this->assertSame(PaymentStatus::PENDING, $transaction->status);
        $this->assertSame(PaymentGateway::CASHFREE, $transaction->gateway);
        $this->assertSame('session_cf_123', $transaction->meta['payment_session_id']);
    }

    public function test_successful_cashfree_frontend_verification_flow(): void
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
            'gateway' => PaymentGateway::CASHFREE,
            'purpose' => PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'ACMEM-CF-TEST-VERIFY',
            'gateway_order_id' => 'ACMEM-CF-TEST-VERIFY',
            'status' => PaymentStatus::PENDING,
            'meta' => ['membership_setting_id' => $setting->id],
        ]);

        // Mock Cashfree order status check
        Http::fake([
            'https://sandbox.cashfree.com/pg/orders/ACMEM-CF-TEST-VERIFY' => Http::response([
                'order_status' => 'PAID',
                'order_amount' => 499.00,
                'cf_order_id' => 'cf_pay_999'
            ], 200)
        ]);

        $response = $this->actingAs($user)->postJson('/payments/verify', [
            'transaction_reference' => 'ACMEM-CF-TEST-VERIFY',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $transaction->refresh();
        $this->assertSame(PaymentStatus::AUTHORIZED, $transaction->status);
        $this->assertSame('cf_pay_999', $transaction->gateway_payment_id);
    }

    public function test_cashfree_webhook_captured_activates_membership(): void
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
            'gateway' => PaymentGateway::CASHFREE,
            'purpose' => PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'ACMEM-CF-WEBHOOK',
            'gateway_order_id' => 'ACMEM-CF-WEBHOOK',
            'status' => PaymentStatus::PENDING,
            'meta' => ['membership_setting_id' => $setting->id],
        ]);

        config([
            'payments.gateways.cashfree.test.secret_key' => 'secret_key'
        ]);

        $payload = [
            'type' => 'PAYMENT_SUCCESS_WEBHOOK',
            'data' => [
                'order' => [
                    'order_id' => 'ACMEM-CF-WEBHOOK',
                    'order_amount' => 499.00,
                ],
                'payment' => [
                    'cf_payment_id' => 'cf_payment_111',
                    'payment_status' => 'SUCCESS',
                    'payment_message' => 'Transaction Successful',
                ]
            ]
        ];

        $payloadString = json_encode($payload);
        $signature = base64_encode(hash_hmac('sha256', $payloadString, 'secret_key', true));

        $response = $this->postJson('/webhooks/cashfree', $payload, [
            'x-cf-signature' => $signature,
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'queued']);

        $log = PaymentWebhookLog::where('transaction_reference', 'ACMEM-CF-WEBHOOK')->first();
        $this->assertNotNull($log);
        $this->assertTrue($log->processed);

        $transaction->refresh();
        $this->assertSame(PaymentStatus::CAPTURED, $transaction->status);
        $this->assertSame('cf_payment_111', $transaction->gateway_payment_id);
        $this->assertTrue($user->fresh()->isMember());

        // Test duplicate webhook protection
        $dupResponse = $this->postJson('/webhooks/cashfree', $payload, [
            'x-cf-signature' => $signature,
        ]);
        $dupResponse->assertOk();
        $dupResponse->assertJson(['status' => 'already_processed']);
    }
}
