<?php

namespace Tests\Feature\Payment;

use App\DTOs\Payments\PaymentVerificationResult;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentPurpose;
use App\Enums\Payment\PaymentStatus;
use App\Models\MembershipSetting;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payment\Gateways\RazorpayGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class RazorpayFrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_payment_initiation_dispatches_checkout_event_for_razorpay(): void
    {
        config(['payments.default_gateway' => 'razorpay']);

        $gatewayMock = Mockery::mock(RazorpayGateway::class);
        $gatewayMock->shouldReceive('getGatewayName')->andReturn('razorpay');
        $gatewayMock->shouldReceive('createPayment')
            ->once()
            ->andReturn(new \App\DTOs\Payments\CreatePaymentResult(
                success: true,
                reference: 'ACMEM-ORDER-999',
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

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Public\UpgradeModal::class)
            ->call('open')
            ->call('processPurchase')
            ->assertDispatched('start-razorpay-checkout');

        $this->assertDatabaseHas('payment_transactions', [
            'gateway_order_id' => 'order_rzp_999',
            'status' => 'pending',
        ]);
    }

    public function test_successful_frontend_verification_flow(): void
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
            'reference' => 'ACMEM-FRONTEND-VERIFY',
            'gateway_order_id' => 'order_rzp_999',
            'status' => PaymentStatus::PENDING,
            'meta' => ['membership_setting_id' => $setting->id],
        ]);

        $gatewayMock = Mockery::mock(RazorpayGateway::class);
        $gatewayMock->shouldReceive('verifyPayment')
            ->once()
            ->andReturn(new PaymentVerificationResult(
                success: true,
                reference: 'ACMEM-FRONTEND-VERIFY',
                gatewayPaymentId: 'pay_rzp_111',
                amount: 499.00,
                message: 'Verified'
            ));

        $this->app->instance(RazorpayGateway::class, $gatewayMock);

        $response = $this->actingAs($user)->postJson('/payments/verify', [
            'razorpay_payment_id' => 'pay_rzp_111',
            'razorpay_order_id' => 'order_rzp_999',
            'razorpay_signature' => 'valid_sig',
            'transaction_reference' => 'ACMEM-FRONTEND-VERIFY',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $transaction->refresh();
        $this->assertSame(PaymentStatus::AUTHORIZED, $transaction->status);
        $this->assertFalse($user->fresh()->isMember());
    }

    public function test_failed_verification_flow(): void
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
            'reference' => 'ACMEM-FRONTEND-FAIL',
            'gateway_order_id' => 'order_rzp_999',
            'status' => PaymentStatus::PENDING,
            'meta' => ['membership_setting_id' => $setting->id],
        ]);

        $gatewayMock = Mockery::mock(RazorpayGateway::class);
        $gatewayMock->shouldReceive('verifyPayment')
            ->once()
            ->andReturn(new PaymentVerificationResult(
                success: false,
                reference: 'ACMEM-FRONTEND-FAIL',
                gatewayPaymentId: 'pay_rzp_111',
                amount: 499.00,
                message: 'Verification failed'
            ));

        $this->app->instance(RazorpayGateway::class, $gatewayMock);

        $response = $this->actingAs($user)->postJson('/payments/verify', [
            'razorpay_payment_id' => 'pay_rzp_111',
            'razorpay_order_id' => 'order_rzp_999',
            'razorpay_signature' => 'invalid_sig',
            'transaction_reference' => 'ACMEM-FRONTEND-FAIL',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);

        $transaction->refresh();
        $this->assertSame(PaymentStatus::FAILED, $transaction->status);
        $this->assertFalse($user->fresh()->isMember());
    }

    public function test_ownership_validation_prevent_forged_requests(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $transaction = PaymentTransaction::create([
            'user_id' => $user1->id,
            'amount' => 499.00,
            'currency' => 'INR',
            'gateway' => PaymentGateway::RAZORPAY,
            'purpose' => PaymentPurpose::MEMBERSHIP_UPGRADE,
            'reference' => 'ACMEM-OWNERSHIP-CHECK',
            'gateway_order_id' => 'order_rzp_999',
            'status' => PaymentStatus::PENDING,
        ]);

        // User 2 tries to verify User 1's transaction reference
        $response = $this->actingAs($user2)->postJson('/payments/verify', [
            'razorpay_payment_id' => 'pay_rzp_111',
            'razorpay_order_id' => 'order_rzp_999',
            'razorpay_signature' => 'valid_sig',
            'transaction_reference' => 'ACMEM-OWNERSHIP-CHECK',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false, 'message' => 'Unauthorized transaction access.']);
    }

    public function test_dummy_gateway_fallback_completes_synchronously(): void
    {
        config(['payments.default_gateway' => 'dummy']);

        $user = User::factory()->create();
        $setting = MembershipSetting::create([
            'title' => 'Premium Pass',
            'price_inr' => 499.00,
            'description' => 'Unlimited access',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Public\UpgradeModal::class)
            ->call('open')
            ->call('processPurchase')
            ->assertNotDispatched('start-razorpay-checkout')
            ->assertSet('paymentSuccess', true);

        $this->assertTrue($user->fresh()->isMember());
    }
}
