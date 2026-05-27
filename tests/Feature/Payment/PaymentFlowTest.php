<?php

namespace Tests\Feature\Payment;

use App\DTOs\Payments\CreatePaymentResult;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentPurpose;
use App\Enums\Payment\PaymentStatus;
use App\Models\MembershipSetting;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Models\UserMembership;
use App\Services\Payment\Exceptions\UnsupportedGatewayException;
use App\Services\Payment\PaymentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentManager $paymentManager;

    protected function setUp(): void
    {
        parent::setUp();
        config(['payments.default_gateway' => 'dummy']);
        $this->paymentManager = app(PaymentManager::class);
    }

    public function test_gateway_resolution(): void
    {
        $gateway = $this->paymentManager->gateway('dummy');
        $this->assertInstanceOf(\App\Services\Payment\Gateways\DummyPaymentGateway::class, $gateway);
        $this->assertSame('dummy', $gateway->getGatewayName());

        $razorpayGateway = $this->paymentManager->gateway('razorpay');
        $this->assertInstanceOf(\App\Services\Payment\Gateways\RazorpayGateway::class, $razorpayGateway);
        $this->assertSame('razorpay', $razorpayGateway->getGatewayName());
    }

    public function test_unsupported_gateway_resolution(): void
    {
        $this->expectException(UnsupportedGatewayException::class);
        $this->paymentManager->gateway('invalid_gateway');
    }

    public function test_successful_dummy_payment_and_membership_activation(): void
    {
        $user = User::factory()->create();
        $setting = MembershipSetting::create([
            'title' => 'Premium Pass',
            'price_inr' => 499.00,
            'description' => 'Unlimited access',
            'is_active' => true,
        ]);

        $cardData = [
            'name' => 'John Doe',
            'number' => '123456789012', // 12 digits (valid)
            'expiry' => '12/28',
            'cvv' => '123',
        ];

        $result = $this->paymentManager->purchaseMembership($user, 499.00, $cardData, $setting->id);

        $this->assertTrue($result->success);
        $this->assertNotNull($result->reference);
        $this->assertSame(499.00, $result->amount);

        // Verify transaction was persisted with CAPTURED status
        $transaction = PaymentTransaction::where('reference', $result->reference)->first();
        $this->assertNotNull($transaction);
        $this->assertSame(PaymentStatus::CAPTURED, $transaction->status);
        $this->assertSame(PaymentGateway::DUMMY, $transaction->gateway);
        $this->assertSame(PaymentPurpose::MEMBERSHIP_UPGRADE, $transaction->purpose);
        $this->assertNotNull($transaction->paid_at);
        $this->assertNull($transaction->failed_at);

        // Verify membership was activated
        $userMembership = UserMembership::where('user_id', $user->id)->first();
        $this->assertNotNull($userMembership);
        $this->assertSame('active', $userMembership->status);
        $this->assertEquals(499.00, $userMembership->amount_paid_inr);
        $this->assertSame($result->reference, $userMembership->payment_reference);

        // Verify user helper isMember() works
        $this->assertTrue($user->fresh()->isMember());
    }

    public function test_failed_dummy_payment(): void
    {
        $user = User::factory()->create();
        $setting = MembershipSetting::create([
            'title' => 'Premium Pass',
            'price_inr' => 499.00,
            'description' => 'Unlimited access',
            'is_active' => true,
        ]);

        $cardData = [
            'name' => 'John Doe',
            'number' => '12345', // Short length (invalid)
            'expiry' => '12/28',
            'cvv' => '123',
        ];

        $result = $this->paymentManager->purchaseMembership($user, 499.00, $cardData, $setting->id);

        $this->assertFalse($result->success);
        $this->assertNull($result->reference);

        // Verify transaction was persisted with FAILED status
        $transaction = PaymentTransaction::where('user_id', $user->id)->first();
        $this->assertNotNull($transaction);
        $this->assertSame(PaymentStatus::FAILED, $transaction->status);
        $this->assertNotNull($transaction->failed_at);
        $this->assertNull($transaction->paid_at);
        $this->assertSame('Invalid card number format.', $transaction->failure_reason);

        // Verify no membership was created
        $userMembership = UserMembership::where('user_id', $user->id)->first();
        $this->assertNull($userMembership);
        $this->assertFalse($user->fresh()->isMember());
    }

    public function test_transaction_model_helpers(): void
    {
        $user = User::factory()->create();
        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'currency' => 'INR',
            'gateway' => PaymentGateway::DUMMY,
            'purpose' => PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'TEST-REF-123',
            'status' => PaymentStatus::INITIATED,
        ]);

        $this->assertFalse($transaction->isCaptured());
        $this->assertFalse($transaction->isFailed());
        $this->assertFalse($transaction->isPending());
        $this->assertTrue($transaction->canBeCaptured());

        $transaction->status = PaymentStatus::PENDING;
        $this->assertTrue($transaction->isPending());
        $this->assertTrue($transaction->canBeCaptured());

        $transaction->status = PaymentStatus::CAPTURED;
        $this->assertTrue($transaction->isCaptured());
        $this->assertFalse($transaction->canBeCaptured());

        $transaction->status = PaymentStatus::FAILED;
        $this->assertTrue($transaction->isFailed());
        $this->assertTrue($transaction->canBeCaptured());
    }
}
