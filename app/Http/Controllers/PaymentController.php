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

        $request->validate([
            'transaction_reference' => 'required|string',
        ]);

        try {
            $success = $this->verifyPayment->execute(Auth::user(), $request->all());

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
