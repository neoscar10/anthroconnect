@extends('layouts.public')

@section('content')
<div class="flex min-h-screen w-full flex-col lg:flex-row bg-stone-50">
    <!-- Left Panel: Artistic & Informational -->
    <div class="relative hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between p-12 pt-24 overflow-hidden bg-gradient-to-br from-stone-200 via-orange-50 to-stone-100 border-r border-stone-200">
        <!-- Background Artistic Overlay -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCNqMI34iUS3H2LolUUmNZuE5wUD-5QwOiSpojeUiRWXOdcOdrrdAI0H0cf-EGPeSL2d_58QoWUesor9wS1Y2L9rd2bKGy4fcVwtiNk7h9q2UyJAxkvAqImhe9sh1eOD6VL29lLcJzWjjwhnZnb6OLd0pzDxz8LazJRaDEd5xFOw0UZPc1dmW4iE-Kb9uyF2De3b2tUtaGABofrax8WzAUL1OQYzyBGOoIZDHjL_2PMtFbKbHEEFR-frnyQ7iTAFgrebOE4mRzzMy8'); background-size: cover; background-position: center;"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-12">
                <div class="bg-orange-800 p-1.5 rounded-lg shadow-lg">
                    <span class="material-symbols-outlined text-white text-2xl">vibration</span>
                </div>
                <span class="text-stone-900 font-bold text-xl tracking-tight uppercase">AnthroConnect</span>
            </div>
            
            <h1 class="font-headline text-5xl xl:text-6xl text-stone-900 leading-tight mb-6">
                Verification <br/>in <span class="text-orange-800 italic">Progress</span>
            </h1>
            
            <p class="text-stone-600 text-lg max-w-md leading-relaxed mb-12 font-body font-medium">We've sent a 6-digit verification code to your WhatsApp. Please enter it to continue your account recovery.</p>
            
            <div class="space-y-8 max-w-sm">
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm">
                        <span class="material-symbols-outlined text-orange-800">sms</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Check WhatsApp</h3>
                        <p class="text-stone-500 text-sm">The code was sent to the number ending in {{ substr(session('password_reset_phone'), -4) }}.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="relative z-10 pt-10">
            <p class="text-stone-400 text-xs font-bold tracking-[0.2em] uppercase">Secure Access © 2024</p>
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div class="flex w-full lg:w-7/12 xl:w-1/2 flex-col items-center justify-center bg-white p-6 md:p-12 lg:p-20 overflow-y-auto">
        <div class="w-full max-w-[440px]">
            <!-- Mobile Header -->
            <div class="lg:hidden flex items-center justify-center gap-2 mb-10">
                <span class="material-symbols-outlined text-orange-800 text-3xl">vibration</span>
                <h1 class="text-2xl font-headline italic text-stone-900">AnthroConnect</h1>
            </div>

            <div class="mb-10 lg:text-left text-center">
                <h2 class="font-headline text-3xl lg:text-4xl text-stone-900 mb-2">Verify OTP</h2>
                <p class="text-stone-500 font-medium font-body leading-relaxed">Enter the 6-digit code sent to your WhatsApp number.</p>
            </div>

            <form class="space-y-8" method="POST" action="{{ route('password.otp.verify') }}">
                @csrf

                <!-- OTP Input -->
                <div class="space-y-4">
                    <label for="otp" class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">6-Digit Code</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl group-focus-within:text-orange-800 transition-colors">pin</span>
                        <input 
                            id="otp"
                            name="otp" 
                            type="text" 
                            maxlength="6"
                            required 
                            autofocus 
                            class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-4 pl-12 pr-4 text-2xl tracking-[0.5em] font-bold text-stone-900 focus:border-orange-800 focus:ring-4 focus:ring-orange-800/5 transition-all outline-none text-center"
                            placeholder="000000"
                        />
                    </div>
                    @error('otp') 
                        <p class="text-xs text-red-600 mt-2 ml-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">error</span>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="pt-2">
                    <button class="w-full rounded-2xl bg-stone-900 py-4 font-bold text-stone-50 shadow-xl shadow-stone-900/20 hover:bg-orange-800 transition-all active:scale-[0.98] uppercase tracking-widest text-sm flex items-center justify-center gap-2" type="submit">
                        <span>Verify & Continue</span>
                        <span class="material-symbols-outlined text-lg">verified</span>
                    </button>
                </div>

                <div class="text-center pt-4">
                    <p class="text-sm text-stone-500 font-medium">
                        Didn't receive the code? 
                        <a href="{{ route('password.request') }}" class="text-orange-800 font-bold hover:underline ml-1">Resend</a>
                    </p>
                </div>
            </form>
        </div>

        <div class="mt-auto pt-12 text-center">
            <p class="text-[10px] text-stone-400 font-bold tracking-[0.2em] uppercase">© 2024 AnthroConnect Security Portal</p>
        </div>
    </div>
</div>
@endsection
