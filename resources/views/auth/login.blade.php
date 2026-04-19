@extends('layouts.public')

@section('content')
<div class="flex min-h-screen w-full flex-col lg:flex-row bg-stone-50" x-data="{ showPassword: false }">
    <!-- Left Panel: Artistic & Informational (Hidden on Mobile) -->
    <div class="relative hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between p-12 pt-24 overflow-hidden bg-gradient-to-br from-stone-200 via-orange-50 to-stone-100 border-r border-stone-200">
        <!-- Background Artistic Overlay -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCNqMI34iUS3H2LolUUmNZuE5wUD-5QwOiSpojeUiRWXOdcOdrrdAI0H0cf-EGPeSL2d_58QoWUesor9wS1Y2L9rd2bKGy4fcVwtiNk7h9q2UyJAxkvAqImhe9sh1eOD6VL29lLcJzWjjwhnZnb6OLd0pzDxz8LazJRaDEd5xFOw0UZPc1dmW4iE-Kb9uyF2De3b2tUtaGABofrax8WzAUL1OQYzyBGOoIZDHjL_2PMtFbKbHEEFR-frnyQ7iTAFgrebOE4mRzzMy8'); background-size: cover; background-position: center;"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-12">
                <div class="bg-orange-800 p-1.5 rounded-lg shadow-lg">
                    <span class="material-symbols-outlined text-white text-2xl">public</span>
                </div>
                <span class="text-stone-900 font-bold text-xl tracking-tight uppercase">AnthroConnect</span>
            </div>
            
            <h1 class="font-headline text-5xl xl:text-6xl text-stone-900 leading-tight mb-6">
                Welcome Back to the <br/>Anthropology <span class="text-orange-800 italic">Community</span>
            </h1>
            
            <p class="text-stone-600 text-lg max-w-md leading-relaxed mb-12 font-body font-medium">Continue exploring anthropology, access your learning pathways, connect with the global community, and discover new perspectives on human cultures and societies.</p>
            
            <div class="space-y-8 max-w-sm">
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm group-hover:border-orange-200 group-hover:bg-orange-50 transition-colors">
                        <span class="material-symbols-outlined text-orange-800">menu_book</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Continue your anthropology learning</h3>
                        <p class="text-stone-500 text-sm">Pick up exactly where you left off in your curriculum.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm group-hover:border-orange-200 group-hover:bg-orange-50 transition-colors">
                        <span class="material-symbols-outlined text-orange-800">forum</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Join community discussions</h3>
                        <p class="text-stone-500 text-sm">Engage with peers and experts across the globe.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm group-hover:border-orange-200 group-hover:bg-orange-50 transition-colors">
                        <span class="material-symbols-outlined text-orange-800">auto_stories</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Access curated research resources</h3>
                        <p class="text-stone-500 text-sm">Direct access to our digital library and archives.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="relative z-10 pt-10">
            <p class="text-stone-400 text-xs font-bold tracking-[0.2em] uppercase">Global Research Portal © 2024</p>
        </div>
    </div>

    <!-- Right Panel: Form (Full width on Mobile) -->
    <div class="flex w-full lg:w-7/12 xl:w-1/2 flex-col items-center justify-center bg-white p-6 md:p-12 lg:p-20 overflow-y-auto">
        <div class="w-full max-w-[440px]">
            <!-- Mobile Header (Logo) -->
            <div class="lg:hidden flex items-center justify-center gap-2 mb-10">
                <span class="material-symbols-outlined text-orange-800 text-3xl">public</span>
                <h1 class="text-2xl font-headline italic text-stone-900">AnthroConnect</h1>
            </div>

            <div class="mb-10 lg:text-left text-center">
                <h2 class="font-headline text-3xl lg:text-4xl text-stone-900 mb-2">Log In to Your Account</h2>
                <p class="text-stone-500 font-medium font-body">Access your anthropology learning dashboard.</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-100 text-green-700 text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form class="space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Address -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">Email Address</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">mail</span>
                        <input name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-3.5 pl-12 pr-4 text-sm focus:border-orange-800 focus:ring-orange-800/20 transition-all outline-none" placeholder="scholar@university.edu" type="email"/>
                    </div>
                    @error('email') <p class="text-xs text-red-600 mt-1 ml-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-1">
                        <label class="text-sm font-bold text-stone-700 uppercase tracking-wider">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-bold text-orange-800 hover:text-orange-900 transition-colors" href="{{ route('password.request') }}">
                                Forgot Password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">lock</span>
                        <input name="password" required :type="showPassword ? 'text' : 'password'" class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-3.5 pl-12 pr-12 text-sm focus:border-orange-800 focus:ring-orange-800/20 transition-all outline-none" placeholder="••••••••"/>
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-orange-800 transition-colors">
                            <span class="material-symbols-outlined text-xl" x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-red-600 mt-1 ml-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center px-1">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-stone-300 text-orange-800 focus:ring-orange-800 transition-all cursor-pointer">
                    <label for="remember_me" class="ml-3 block text-sm text-stone-600 font-medium cursor-pointer">Remember me for 30 days</label>
                </div>

                <button class="w-full rounded-2xl bg-stone-900 py-4 font-bold text-stone-50 shadow-xl shadow-stone-900/20 hover:bg-orange-800 transition-all active:scale-[0.98] uppercase tracking-widest text-sm" type="submit">
                    Log In
                </button>
            </form>


            <div class="text-center">
                <p class="text-sm text-stone-500 font-medium font-body italic">
                    Don’t have an account? 
                    <a class="font-bold text-orange-800 hover:text-orange-900 underline underline-offset-4 ml-1 transition-colors" href="{{ route('register') }}">
                        Create one
                    </a>
                </p>
            </div>
        </div>

        <div class="mt-auto pt-12 text-center">
            <p class="text-[10px] text-stone-400 font-bold tracking-[0.2em] uppercase">© 2024 AnthroConnect Global Research Portal</p>
        </div>
    </div>
</div>
@endsection
