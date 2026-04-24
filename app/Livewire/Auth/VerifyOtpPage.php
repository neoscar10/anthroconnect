<?php

namespace App\Livewire\Auth;

use App\Services\Auth\WhatsappAuthService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class VerifyOtpPage extends Component
{
    public $otp;

    public function mount()
    {
        if (Auth::user()?->hasCompletedOtpVerification()) {
            return redirect()->route('dashboard');
        }
    }

    public function verify(WhatsappAuthService $authService)
    {
        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        $authService->verifyDummyOtp($user, $this->otp);

        session()->flash('success', 'Your WhatsApp number has been verified successfully.');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.verify-otp-page')
            ->layout('layouts.public');
    }
}
