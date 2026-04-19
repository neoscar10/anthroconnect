<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->hasAnyRole(['Super Admin', 'Admin'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            // Not an admin, log them out and throw error
            Auth::logout();
            
            throw ValidationException::withMessages([
                'email' => __('This portal is reserved for authorized archivists only.'),
            ]);
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
