<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, \App\Services\Auth\WhatsappAuthService $authService, \App\Services\Onboarding\UserOnboardingService $onboardingService): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_phone' => ['required', 'string', 'max:20', 'unique:users,whatsapp_phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['nullable', 'string', 'in:student,upsc_aspirant,researcher,educator,enthusiast'],
        ]);

        $user = $authService->register([
            'name' => $request->name,
            'whatsapp_phone' => $request->whatsapp_phone,
            'password' => $request->password,
        ]);

        // Manually update user_type since service might not handle it all
        if ($request->user_type) {
            $user->update(['user_type' => $request->user_type]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.otp.verify');
    }
}
