@extends('layouts.public')

@section('content')
<div class="flex min-h-screen w-full flex-col lg:flex-row bg-stone-50">
    <!-- Left Panel: Artistic & Informational (Consistent with Login/Register) -->
    <div class="relative hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between p-12 pt-24 overflow-hidden bg-gradient-to-br from-stone-200 via-orange-50 to-stone-100 border-r border-stone-200">
        <!-- Background Artistic Overlay -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCNqMI34iUS3H2LolUUmNZuE5wUD-5QwOiSpojeUiRWXOdcOdrrdAI0H0cf-EGPeSL2d_58QoWUesor9wS1Y2L9rd2bKGy4fcVwtiNk7h9q2UyJAxkvAqImhe9sh1eOD6VL29lLcJzWjjwhnZnb6OLd0pzDxz8LazJRaDEd5xFOw0UZPc1dmW4iE-Kb9uyF2De3b2tUtaGABofrax8WzAUL1OQYzyBGOoIZDHjL_2PMtFbKbHEEFR-frnyQ7iTAFgrebOE4mRzzMy8'); background-size: cover; background-position: center;"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-12">
                <div class="bg-orange-800 p-1.5 rounded-lg shadow-lg">
                    <span class="material-symbols-outlined text-white text-2xl">history_edu</span>
                </div>
                <span class="text-stone-900 font-bold text-xl tracking-tight uppercase">AnthroConnect</span>
            </div>
            
            <h1 class="font-headline text-5xl xl:text-6xl text-stone-900 leading-tight mb-6">
                Recover Your <br/>Scholar <span class="text-orange-800 italic">Account</span>
            </h1>
            
            <p class="text-stone-600 text-lg max-w-md leading-relaxed mb-12 font-body font-medium">Lost your way? Don't worry. Provide your registered WhatsApp number, and we'll help you regain access to your research and learning pathways.</p>
            
            <div class="space-y-8 max-w-sm">
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm">
                        <span class="material-symbols-outlined text-orange-800">security</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Secure Recovery</h3>
                        <p class="text-stone-500 text-sm">We use encrypted tokens to ensure only you can reset your password.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm">
                        <span class="material-symbols-outlined text-orange-800">support_agent</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Need help?</h3>
                        <p class="text-stone-500 text-sm">Our support team is available if you no longer have access to your phone.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="relative z-10 pt-10">
            <p class="text-stone-400 text-xs font-bold tracking-[0.2em] uppercase">Account Security © 2024</p>
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div class="flex w-full lg:w-7/12 xl:w-1/2 flex-col items-center justify-center bg-white p-6 md:p-12 lg:p-20 overflow-y-auto">
        <div class="w-full max-w-[440px]">
            <!-- Mobile Header -->
            <div class="lg:hidden flex items-center justify-center gap-2 mb-10">
                <span class="material-symbols-outlined text-orange-800 text-3xl">history_edu</span>
                <h1 class="text-2xl font-headline italic text-stone-900">AnthroConnect</h1>
            </div>

            <div class="mb-10 lg:text-left text-center">
                <h2 class="font-headline text-3xl lg:text-4xl text-stone-900 mb-2">Forgot Password?</h2>
                <p class="text-stone-500 font-medium font-body leading-relaxed">No problem. Just enter your registered WhatsApp phone number and we will send you a password reset link.</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-8 p-5 rounded-2xl bg-green-50 border border-green-100 flex items-start gap-4">
                    <span class="material-symbols-outlined text-green-600 mt-0.5">check_circle</span>
                    <p class="text-green-800 text-sm font-medium leading-relaxed">
                        {{ session('status') }}
                    </p>
                </div>
            @endif

            <form class="space-y-8" method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- WhatsApp Phone Number -->
                <div class="space-y-3">
                    <label for="whatsapp_phone" class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">WhatsApp Phone Number</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl group-focus-within:text-orange-800 transition-colors">phone_iphone</span>
                        <input 
                            id="whatsapp_phone"
                            name="whatsapp_phone" 
                            type="text" 
                            value="{{ old('whatsapp_phone') }}" 
                            required 
                            autofocus 
                            class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-4 pl-12 pr-4 text-sm focus:border-orange-800 focus:ring-4 focus:ring-orange-800/5 transition-all outline-none placeholder:text-stone-300"
                            placeholder="e.g. +91 98765 43210"
                        />
                    </div>
                    @error('whatsapp_phone') 
                        <p class="text-xs text-red-600 mt-2 ml-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">error</span>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="pt-2">
                    <button class="w-full rounded-2xl bg-stone-900 py-4 font-bold text-stone-50 shadow-xl shadow-stone-900/20 hover:bg-orange-800 transition-all active:scale-[0.98] uppercase tracking-widest text-sm flex items-center justify-center gap-2" type="submit">
                        <span>Send Verification Code</span>
                        <span class="material-symbols-outlined text-lg">vibration</span>
                    </button>
                </div>

                <div class="text-center pt-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-bold text-stone-500 hover:text-stone-900 transition-colors group">
                        <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        Back to Log In
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-auto pt-12 text-center">
            <p class="text-[10px] text-stone-400 font-bold tracking-[0.2em] uppercase">© 2024 AnthroConnect Security Portal</p>
        </div>
    </div>
</div>
@endsection
