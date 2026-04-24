<div class="flex min-h-screen w-full flex-col lg:flex-row bg-stone-50">
    <!-- Left Panel: Artistic & Informational -->
    <div class="relative hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between p-12 pt-24 overflow-hidden bg-gradient-to-br from-stone-200 via-orange-50 to-stone-100 border-r border-stone-200">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCNqMI34iUS3H2LolUUmNZuE5wUD-5QwOiSpojeUiRWXOdcOdrrdAI0H0cf-EGPeSL2d_58QoWUesor9wS1Y2L9rd2bKGy4fcVwtiNk7h9q2UyJAxkvAqImhe9sh1eOD6VL29lLcJzWjjwhnZnb6OLd0pzDxz8LazJRaDEd5xFOw0UZPc1dmW4iE-Kb9uyF2De3b2tUtaGABofrax8WzAUL1OQYzyBGOoIZDHjL_2PMtFbKbHEEFR-frnyQ7iTAFgrebOE4mRzzMy8'); background-size: cover; background-position: center;"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-12">
                <div class="bg-orange-800 p-1.5 rounded-lg shadow-lg">
                    <span class="material-symbols-outlined text-white text-2xl">verified_user</span>
                </div>
                <span class="text-stone-900 font-bold text-xl tracking-tight uppercase">AnthroConnect</span>
            </div>
            
            <h1 class="font-headline text-5xl xl:text-6xl text-stone-900 leading-tight mb-6">
                Security & <br/><span class="text-orange-800 italic">Verification</span>
            </h1>
            
            <p class="text-stone-600 text-lg max-w-md leading-relaxed mb-12 font-body font-medium">We take your security seriously. Verifying your WhatsApp number ensures a trusted community of anthropology scholars.</p>
        </div>
        
        <div class="relative z-10 pt-10">
            <p class="text-stone-400 text-xs font-bold tracking-[0.2em] uppercase">Trusted Research © 2024</p>
        </div>
    </div>

    <!-- Right Panel: Verification Form -->
    <div class="flex w-full lg:w-7/12 xl:w-1/2 flex-col items-center justify-center bg-white p-6 md:p-12 lg:p-20">
        <div class="w-full max-w-[440px]">
            <div class="mb-10 text-center lg:text-left">
                <h2 class="font-headline text-3xl lg:text-4xl text-stone-900 mb-2">Verify WhatsApp</h2>
                <p class="text-stone-500 font-medium font-body">We've sent a code to <span class="font-bold text-stone-900">{{ auth()->user()->whatsapp_phone }}</span></p>
            </div>

            <div class="mb-8 p-4 rounded-2xl bg-orange-50 border border-orange-100 text-orange-800 text-sm font-medium">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined">info</span>
                    <p>Development Mode: Enter any <strong>6-digit code</strong> (e.g., 000000) to verify your account.</p>
                </div>
            </div>

            <form wire:submit.prevent="verify" class="space-y-8">
                <div class="space-y-4 text-center">
                    <label class="text-sm font-bold text-stone-700 uppercase tracking-widest">Enter Verification Code</label>
                    <div class="flex justify-center">
                        <input 
                            type="text" 
                            maxlength="6" 
                            wire:model="otp" 
                            class="w-full max-w-[300px] text-center text-4xl font-headline tracking-[0.5em] rounded-2xl border-stone-200 bg-stone-50/50 py-6 focus:border-orange-800 focus:ring-orange-800/20 transition-all outline-none @error('otp') border-red-500 @enderror" 
                            placeholder="000000"
                        />
                    </div>
                    @error('otp') <p class="text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <button class="w-full rounded-2xl bg-stone-900 py-4 font-bold text-stone-50 shadow-xl shadow-stone-900/20 hover:bg-orange-800 transition-all active:scale-[0.98] uppercase tracking-widest text-sm" type="submit">
                    Verify & Continue
                </button>
            </form>

            <div class="mt-10 text-center">
                <p class="text-sm text-stone-500 font-medium">
                    Didn't receive the code? 
                    <button class="font-bold text-orange-800 hover:text-orange-900 underline underline-offset-4 ml-1">
                        Resend via WhatsApp
                    </button>
                </p>
            </div>

            <div class="mt-8 pt-8 border-t border-stone-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-stone-400 hover:text-stone-600 uppercase tracking-widest flex items-center justify-center gap-2 mx-auto">
                        <span class="material-symbols-outlined text-sm">logout</span>
                        Log out and try again
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
