<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'whatsapp_phone' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $phone = trim($request->whatsapp_phone);
        $phone = preg_replace('/\s+/', '', $phone);

        // Custom flow for OTP-verified resets
        if ($request->token === 'otp-verified' && session('password_reset_authorized')) {
            $user = User::where('whatsapp_phone', $phone)->first();

            if (!$user || $phone !== session('password_reset_phone')) {
                return back()->withErrors(['whatsapp_phone' => 'Invalid recovery session.']);
            }

            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));

            session()->forget(['password_reset_phone', 'password_reset_authorized']);

            return redirect()->route('login')->with('status', 'Your password has been reset successfully.');
        }

        // Fallback to standard Laravel reset logic if a real token is provided
        $status = Password::reset(
            $request->only('whatsapp_phone', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('whatsapp_phone'))
                        ->withErrors(['whatsapp_phone' => __($status)]);
    }
}
