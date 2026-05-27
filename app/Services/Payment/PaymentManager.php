<?php

namespace App\Services\Payment;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\DTOs\Payments\CreatePaymentResult;
use App\DTOs\Payments\PaymentTransactionData;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentPurpose;
use App\Models\User;
use App\Services\Payment\Actions\CreatePaymentTransactionAction;
use App\Services\Payment\Actions\MarkTransactionCapturedAction;
use App\Services\Payment\Actions\MarkTransactionFailedAction;
use App\Services\Payment\Actions\ActivateMembershipFromPaymentAction;
use App\Services\Payment\Exceptions\UnsupportedGatewayException;
use App\Services\Payment\Gateways\DummyPaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Services\Payment\Gateways\RazorpayGateway;

class PaymentManager
{
    protected CreatePaymentTransactionAction $createTransactionAction;
    protected MarkTransactionCapturedAction $markCapturedAction;
    protected MarkTransactionFailedAction $markFailedAction;
    protected ActivateMembershipFromPaymentAction $activateMembershipAction;

    public function __construct(
        CreatePaymentTransactionAction $createTransactionAction,
        MarkTransactionCapturedAction $markCapturedAction,
        MarkTransactionFailedAction $markFailedAction,
        ActivateMembershipFromPaymentAction $activateMembershipAction
    ) {
        $this->createTransactionAction = $createTransactionAction;
        $this->markCapturedAction = $markCapturedAction;
        $this->markFailedAction = $markFailedAction;
        $this->activateMembershipAction = $activateMembershipAction;
    }

    /**
     * Resolve the active payment gateway.
     */
    public function gateway(?string $name = null): PaymentGatewayInterface
    {
        $name = $name ?? config('payments.default_gateway', 'dummy');

        if ($name === 'dummy') {
            return app(DummyPaymentGateway::class);
        }

        if ($name === 'razorpay') {
            return app(RazorpayGateway::class);
        }

        throw new UnsupportedGatewayException("Gateway [{$name}] is not supported.");
    }

    /**
     * Process purchase/checkout flow.
     */
    public function purchaseMembership(User $user, float $amount, array $cardData, int $settingId): CreatePaymentResult
    {
        $gatewayName = config('payments.default_gateway', 'dummy');
        $gateway = $this->gateway($gatewayName);
        $currency = config('payments.currency', 'INR');

        // Generate local unique reference
        $reference = 'ACMEM-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // 1. Create transaction in DB (initiated state)
        $transactionData = new PaymentTransactionData(
            userId: $user->id,
            amount: $amount,
            currency: $currency,
            gateway: PaymentGateway::from($gatewayName),
            purpose: PaymentPurpose::MEMBERSHIP_UPGRADE,
            reference: $reference,
            meta: [
                'membership_setting_id' => $settingId,
            ]
        );

        $transaction = $this->createTransactionAction->execute($transactionData);

        try {
            // 2. Call gateway
            $result = $gateway->createPayment([
                'card_data' => $cardData,
                'amount' => $amount,
                'reference' => $reference,
                'user_id' => $user->id,
                'purpose' => 'membership_upgrade',
                'membership_setting_id' => $settingId,
            ]);

            if ($result->success) {
                if ($gatewayName === 'razorpay') {
                    // Update to pending and store the razorpay order_id
                    $transaction->update([
                        'status' => \App\Enums\Payment\PaymentStatus::PENDING,
                        'gateway_order_id' => $result->gatewayOrderId,
                        'meta' => array_merge($transaction->meta ?? [], $result->meta),
                    ]);
                } else {
                    // Dummy gateway - mark captured and activate membership immediately
                    DB::transaction(function () use ($transaction, $result) {
                        $this->markCapturedAction->execute(
                            transaction: $transaction,
                            gatewayPaymentId: $result->gatewayPaymentId,
                            additionalMeta: $result->meta
                        );

                        $this->activateMembershipAction->execute($transaction);
                    });
                }
            } else {
                // Mark transaction failed
                $this->markFailedAction->execute(
                    transaction: $transaction,
                    reason: $result->message ?? 'Payment failed.'
                );
            }

            return $result;

        } catch (\Exception $e) {
            // Handle unexpected gateway errors gracefully
            $this->markFailedAction->execute(
                transaction: $transaction,
                reason: $e->getMessage()
            );

            return new CreatePaymentResult(
                success: false,
                reference: null,
                amount: $amount,
                message: 'Payment execution failed: ' . $e->getMessage()
            );
        }
    }
}
