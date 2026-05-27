<?php

namespace App\Http\Controllers;

use App\Services\Payment\Actions\VerifyPaymentAction;
use App\Services\Payment\Exceptions\PaymentVerificationException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected VerifyPaymentAction $verifyPayment;

    public function __construct(VerifyPaymentAction $verifyPayment)
    {
        $this->verifyPayment = $verifyPayment;
    }

    public function verify(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'transaction_reference' => 'required|string',
        ]);

        try {
            $success = $this->verifyPayment->execute(Auth::user(), $validated);

            return response()->json([
                'success' => $success,
                'message' => 'Payment verified successfully.'
            ]);

        } catch (PaymentVerificationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification process failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
