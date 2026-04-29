<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'whatsapp_phone' => ['required', 'string'],
        ]);

        $phone = trim($request->whatsapp_phone);
        $phone = preg_replace('/\s+/', '', $phone);

        $user = \App\Models\User::where('whatsapp_phone', $phone)->first();

        if (!$user) {
            return back()->withInput($request->only('whatsapp_phone'))
                ->withErrors(['whatsapp_phone' => 'We could not find a user with that WhatsApp number.']);
        }

        // Store phone in session for OTP verification
        session(['password_reset_phone' => $phone]);

        return redirect()->route('password.otp.verify');
    }

    /**
     * Display the OTP verification view.
     */
    public function showOtpForm(): View|RedirectResponse
    {
        if (!session()->has('password_reset_phone')) {
            return redirect()->route('password.request');
        }

        return view('auth.forgot-password-otp');
    }

    /**
     * Verify the dummy OTP.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        // Logic for dummy OTP (any 6 digit number works as per user requirement)
        // In a real app, you would check against a stored OTP.
        
        session(['password_reset_authorized' => true]);

        return redirect()->route('password.reset', [
            'token' => 'otp-verified',
            'whatsapp_phone' => session('password_reset_phone')
        ]);
    }
}
