@extends('layouts.public')

@section('content')
<div class="flex min-h-screen w-full flex-col lg:flex-row bg-stone-50" x-data="{ showPassword: false, showConfirmPassword: false }">
    <!-- Left Panel: Artistic & Informational -->
    <div class="relative hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between p-12 pt-24 overflow-hidden bg-gradient-to-br from-stone-200 via-orange-50 to-stone-100 border-r border-stone-200">
        <!-- Background Artistic Overlay -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCNqMI34iUS3H2LolUUmNZuE5wUD-5QwOiSpojeUiRWXOdcOdrrdAI0H0cf-EGPeSL2d_58QoWUesor9wS1Y2L9rd2bKGy4fcVwtiNk7h9q2UyJAxkvAqImhe9sh1eOD6VL29lLcJzWjjwhnZnb6OLd0pzDxz8LazJRaDEd5xFOw0UZPc1dmW4iE-Kb9uyF2De3b2tUtaGABofrax8WzAUL1OQYzyBGOoIZDHjL_2PMtFbKbHEEFR-frnyQ7iTAFgrebOE4mRzzMy8'); background-size: cover; background-position: center;"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-12">
                <div class="bg-orange-800 p-1.5 rounded-lg shadow-lg">
                    <span class="material-symbols-outlined text-white text-2xl">lock_reset</span>
                </div>
                <span class="text-stone-900 font-bold text-xl tracking-tight uppercase">AnthroConnect</span>
            </div>
            
            <h1 class="font-headline text-5xl xl:text-6xl text-stone-900 leading-tight mb-6">
                Redefine Your <br/>Scholar <span class="text-orange-800 italic">Security</span>
            </h1>
            
            <p class="text-stone-600 text-lg max-w-md leading-relaxed mb-12 font-body font-medium">Create a strong, unique password to protect your academic contributions and personal research data.</p>
            
            <div class="space-y-8 max-w-sm">
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm">
                        <span class="material-symbols-outlined text-orange-800">verified_user</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Identity Verified</h3>
                        <p class="text-stone-500 text-sm">Your secure token has been validated. You may now choose a new password.</p>
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
                <span class="material-symbols-outlined text-orange-800 text-3xl">lock_reset</span>
                <h1 class="text-2xl font-headline italic text-stone-900">AnthroConnect</h1>
            </div>

            <div class="mb-10 lg:text-left text-center">
                <h2 class="font-headline text-3xl lg:text-4xl text-stone-900 mb-2">Set New Password</h2>
                <p class="text-stone-500 font-medium font-body leading-relaxed">Ensure your new password is at least 8 characters long and contains a mix of symbols and letters.</p>
            </div>

            <form class="space-y-6" method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- WhatsApp Phone Number -->
                <div class="space-y-2">
                    <label for="whatsapp_phone" class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">WhatsApp Phone Number</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl group-focus-within:text-orange-800 transition-colors">phone_iphone</span>
                        <input 
                            id="whatsapp_phone"
                            name="whatsapp_phone" 
                            type="text" 
                            value="{{ old('whatsapp_phone', $request->whatsapp_phone) }}" 
                            required 
                            autofocus 
                            class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-4 pl-12 pr-4 text-sm focus:border-orange-800 focus:ring-4 focus:ring-orange-800/5 transition-all outline-none"
                            placeholder="e.g. +91 98765 43210"
                        />
                    </div>
                    @error('whatsapp_phone') <p class="text-xs text-red-600 mt-2 ml-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">New Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl group-focus-within:text-orange-800 transition-colors">lock</span>
                        <input 
                            id="password"
                            name="password" 
                            required 
                            :type="showPassword ? 'text' : 'password'" 
                            class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-4 pl-12 pr-12 text-sm focus:border-orange-800 focus:ring-4 focus:ring-orange-800/5 transition-all outline-none"
                            placeholder="••••••••"
                        />
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-orange-800 transition-colors">
                            <span class="material-symbols-outlined text-xl" x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-red-600 mt-2 ml-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">Confirm New Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl group-focus-within:text-orange-800 transition-colors">shield_lock</span>
                        <input 
                            id="password_confirmation"
                            name="password_confirmation" 
                            required 
                            :type="showConfirmPassword ? 'text' : 'password'" 
                            class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-4 pl-12 pr-12 text-sm focus:border-orange-800 focus:ring-4 focus:ring-orange-800/5 transition-all outline-none"
                            placeholder="••••••••"
                        />
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-orange-800 transition-colors">
                            <span class="material-symbols-outlined text-xl" x-text="showConfirmPassword ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button class="w-full rounded-2xl bg-stone-900 py-4 font-bold text-stone-50 shadow-xl shadow-stone-900/20 hover:bg-orange-800 transition-all active:scale-[0.98] uppercase tracking-widest text-sm flex items-center justify-center gap-2" type="submit">
                        <span>Update Password</span>
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-auto pt-12 text-center">
            <p class="text-[10px] text-stone-400 font-bold tracking-[0.2em] uppercase">© 2024 AnthroConnect Security Portal</p>
        </div>
    </div>
</div>
@endsection
