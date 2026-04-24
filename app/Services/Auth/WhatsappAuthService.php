<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class WhatsappAuthService
{
    /**
     * Normalize the phone number.
     */
    public function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        $phone = preg_replace('/\s+/', '', $phone);
        return $phone;
    }

    /**
     * Register a new user with WhatsApp phone.
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'whatsapp_phone' => $this->normalizePhone($data['whatsapp_phone']),
            'password' => Hash::make($data['password']),
            'user_type' => 'user', // Default type
            'status' => 'active',
        ]);
    }

    /**
     * Login a user by WhatsApp phone.
     */
    public function login(string $phone, string $password, bool $remember = false): User
    {
        $phone = $this->normalizePhone($phone);
        $user = User::where('whatsapp_phone', $phone)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'whatsapp_phone' => 'The provided credentials are incorrect.',
            ]);
        }

        Auth::login($user, $remember);

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return $user;
    }

    /**
     * Verify a dummy 6-digit OTP.
     */
    public function verifyDummyOtp(User $user, string $otp): void
    {
        if (strlen($otp) !== 6 || !is_numeric($otp)) {
            throw ValidationException::withMessages([
                'otp' => 'The OTP must be a 6-digit number.',
            ]);
        }

        $user->forceFill([
            'whatsapp_phone_verified_at' => now(),
            'otp_verified_at' => now(),
        ])->save();
    }
}
