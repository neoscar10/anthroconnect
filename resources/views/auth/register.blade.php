@extends('layouts.public')

@section('content')
<div class="flex min-h-screen w-full flex-col lg:flex-row bg-stone-50" x-data="{ userType: '{{ old('user_type', 'student') }}', showPassword: false, showConfirmPassword: false }">
    <!-- Left Panel: Artistic & Informational (Hidden on Mobile) -->
    <div class="relative hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between p-12 pt-24 overflow-hidden bg-gradient-to-br from-stone-200 via-orange-50 to-stone-100 border-r border-stone-200">
        <!-- Background Artistic Overlay -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCNqMI34iUS3H2LolUUmNZuE5wUD-5QwOiSpojeUiRWXOdcOdrrdAI0H0cf-EGPeSL2d_58QoWUesor9wS1Y2L9rd2bKGy4fcVwtiNk7h9q2UyJAxkvAqImhe9sh1eOD6VL29lLcJzWjjwhnZnb6OLd0pzDxz8LazJRaDEd5xFOw0UZPc1dmW4iE-Kb9uyF2De3b2tUtaGABofrax8WzAUL1OQYzyBGOoIZDHjL_2PMtFbKbHEEFR-frnyQ7iTAFgrebOE4mRzzMy8'); background-size: cover; background-position: center;"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-12">
                <div class="bg-orange-800 p-1.5 rounded-lg shadow-lg">
                    <span class="material-symbols-outlined text-white text-2xl">account_balance</span>
                </div>
                <span class="text-stone-900 font-bold text-xl tracking-tight uppercase">AnthroConnect</span>
            </div>
            
            <h1 class="font-headline text-5xl xl:text-6xl text-stone-900 leading-tight mb-6">
                Join the Global <br/>Anthropology <span class="text-orange-800 italic">Community</span>
            </h1>
            
            <p class="text-stone-600 text-lg max-w-md leading-relaxed mb-12 font-body font-medium">Create your account to explore anthropology, connect with learners and researchers, access curated knowledge, and participate in a global community.</p>
            
            <div class="space-y-8 max-w-sm">
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm group-hover:border-orange-200 group-hover:bg-orange-50 transition-colors">
                        <span class="material-symbols-outlined text-orange-800">book_2</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Structured Learning</h3>
                        <p class="text-stone-500 text-sm">Access curated knowledge pathways and academic modules.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm group-hover:border-orange-200 group-hover:bg-orange-50 transition-colors">
                        <span class="material-symbols-outlined text-orange-800">public</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Global Resources</h3>
                        <p class="text-stone-500 text-sm">Connect with research papers and archives from around the world.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start group">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white border border-stone-200 shadow-sm group-hover:border-orange-200 group-hover:bg-orange-50 transition-colors">
                        <span class="material-symbols-outlined text-orange-800">forum</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900">Expert Insights</h3>
                        <p class="text-stone-500 text-sm">Participate in deep academic discourse with subject experts.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="relative z-10 pt-10">
            <p class="text-stone-400 text-xs font-bold tracking-[0.2em] uppercase">Academic Excellence © 2024</p>
        </div>
    </div>

    <!-- Right Panel: Form (Full width on Mobile) -->
    <div class="flex w-full lg:w-7/12 xl:w-1/2 flex-col items-center justify-center bg-white p-6 md:p-12 lg:p-20 overflow-y-auto">
        <div class="w-full max-w-[520px]">
            <!-- Mobile Header (Logo) -->
            <div class="lg:hidden flex items-center justify-center gap-2 mb-10">
                <span class="material-symbols-outlined text-orange-800 text-3xl">account_balance</span>
                <h1 class="text-2xl font-headline italic text-stone-900">AnthroConnect</h1>
            </div>

            <div class="mb-10 lg:text-left text-center">
                <h2 class="font-headline text-3xl lg:text-4xl text-stone-900 mb-2">Create Your Account</h2>
                <p class="text-stone-500 font-medium font-body">Start exploring anthropology today.</p>
            </div>

            <!-- Social Sign Up (Simplified for Laravel integration) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <button class="flex items-center justify-center gap-3 rounded-xl border border-stone-200 px-4 py-3 hover:bg-stone-50 transition-all font-bold text-sm text-stone-700 active:scale-95">
                    <img class="h-5 w-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0bD0S24pHid9HDBLz_EmpZqaHHChktGYVvSvg0w1nWFHJ-oMEV3I_xgOljGsNxp6IwAcBx-0UmFzhDnKCt5dhRVZRuPbh7xTwXA7BqmQ3IXtNx-bGnR4Kj1VBZjeD09Z5IIS36gf5A9U8PPaYeCn5_5f0jE_6o6FmAhbhGKiB9yFhVAQeJUZZ23r_HEhmpVB1ZNI23xZicts77uKFMC2tyOLkN70y6NDmcBluKES-O1yzjMbTLVrYsDOammDaMXwPT1bisp3tx5f4" alt="Google">
                    <span>Google</span>
                </button>
                <button class="flex items-center justify-center gap-3 rounded-xl border border-stone-200 px-4 py-3 hover:bg-stone-50 transition-all font-bold text-sm text-stone-700 active:scale-95">
                    <img class="h-5 w-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDy4SoyRefA1bWurHpYdZiZm8zI9jgZazuewq6D1efSMkDy9NF_2e9yFXSBzUuu9QrV2D76PhcdDpDIDofKV7NuGPiDC0oIYXpQsoFeHZ7Kr5_AZlVoEVQE2HUbaDfY4h6MMNllJZdQ68hBZGmxhfKsLhxbRiAoOcTL6Rzyn9SaA99dvGVIKy-FoG8iIbYjkQRlpJlTttKvR9CyRuFCfR6DYJRvOk9c3ZdY9K84QHhgLszfxwf2FxlcbIsRaS68-4nFfNn5eV0cyMRr" alt="LinkedIn">
                    <span>LinkedIn</span>
                </button>
            </div>

            <div class="relative mb-8 text-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-stone-100"></div>
                </div>
                <span class="relative bg-white px-4 text-xs font-bold uppercase tracking-widest text-stone-400">or sign up with</span>
            </div>

            <!-- Registration Form -->
            <form class="space-y-6" method="POST" action="{{ route('register') }}">
                @csrf
                
                <input type="hidden" name="user_type" x-model="userType">

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">Full Name</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">person</span>
                            <input name="name" value="{{ old('name') }}" required autofocus class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-3.5 pl-12 pr-4 text-sm focus:border-orange-800 focus:ring-orange-800/20 transition-all outline-none" placeholder="John Doe" type="text"/>
                        </div>
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">mail</span>
                            <input name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-3.5 pl-12 pr-4 text-sm focus:border-orange-800 focus:ring-orange-800/20 transition-all outline-none" placeholder="john@university.edu" type="email"/>
                        </div>
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">lock</span>
                            <input name="password" required :type="showPassword ? 'text' : 'password'" class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-3.5 pl-12 pr-12 text-sm focus:border-orange-800 focus:ring-orange-800/20 transition-all outline-none" placeholder="••••••••"/>
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-orange-800 transition-colors">
                                <span class="material-symbols-outlined text-xl" x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">Confirm Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-xl">shield_lock</span>
                            <input name="password_confirmation" required :type="showConfirmPassword ? 'text' : 'password'" class="w-full rounded-2xl border-stone-200 bg-stone-50/50 py-3.5 pl-12 pr-12 text-sm focus:border-orange-800 focus:ring-orange-800/20 transition-all outline-none" placeholder="••••••••"/>
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-orange-800 transition-colors">
                                <span class="material-symbols-outlined text-xl" x-text="showConfirmPassword ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- User Role Selection -->
                <div class="space-y-4 pt-2">
                    <label class="text-sm font-bold text-stone-700 uppercase tracking-wider ml-1">I am joining as:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <button type="button" @click="userType = 'student'" :class="userType === 'student' ? 'border-orange-800 bg-orange-50 text-orange-900 ring-4 ring-orange-800/5' : 'border-stone-100 bg-stone-50/50 text-stone-500 hover:border-orange-200'" class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 p-4 transition-all">
                            <span class="material-symbols-outlined text-2xl" :class="userType === 'student' ? 'text-orange-800' : 'text-stone-300'">school</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Student</span>
                        </button>
                        
                        <button type="button" @click="userType = 'upsc_aspirant'" :class="userType === 'upsc_aspirant' ? 'border-orange-800 bg-orange-50 text-orange-900 ring-4 ring-orange-800/5' : 'border-stone-100 bg-stone-50/50 text-stone-500 hover:border-orange-200'" class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 p-4 transition-all">
                            <span class="material-symbols-outlined text-2xl" :class="userType === 'upsc_aspirant' ? 'text-orange-800' : 'text-stone-300'">history_edu</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">UPSC Aspirant</span>
                        </button>

                        <button type="button" @click="userType = 'researcher'" :class="userType === 'researcher' ? 'border-orange-800 bg-orange-50 text-orange-900 ring-4 ring-orange-800/5' : 'border-stone-100 bg-stone-50/50 text-stone-500 hover:border-orange-200'" class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 p-4 transition-all">
                            <span class="material-symbols-outlined text-2xl" :class="userType === 'researcher' ? 'text-orange-800' : 'text-stone-300'">biotech</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Researcher</span>
                        </button>

                        <button type="button" @click="userType = 'educator'" :class="userType === 'educator' ? 'border-orange-800 bg-orange-50 text-orange-900 ring-4 ring-orange-800/5' : 'border-stone-100 bg-stone-50/50 text-stone-500 hover:border-orange-200'" class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 p-4 transition-all">
                            <span class="material-symbols-outlined text-2xl" :class="userType === 'educator' ? 'text-orange-800' : 'text-stone-300'">record_voice_over</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Educator</span>
                        </button>

                        <button type="button" @click="userType = 'enthusiast'" :class="userType === 'enthusiast' ? 'border-orange-800 bg-orange-50 text-orange-900 ring-4 ring-orange-800/5' : 'border-stone-100 bg-stone-50/50 text-stone-500 hover:border-orange-200'" class="group flex flex-col items-center justify-center gap-2 rounded-2xl border-2 p-4 transition-all sm:col-span-2 col-span-1">
                            <span class="material-symbols-outlined text-2xl" :class="userType === 'enthusiast' ? 'text-orange-800' : 'text-stone-300'">explore</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">Enthusiast</span>
                        </button>
                    </div>
                    @error('user_type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-start gap-3 py-2">
                    <input class="mt-1 rounded border-stone-200 text-orange-800 focus:ring-orange-800 transition-all" id="terms" type="checkbox" required/>
                    <label class="text-xs text-stone-500 leading-relaxed font-medium" for="terms">
                        By creating an account, I agree to AnthroConnect's <a class="text-orange-800 font-bold underline decoration-orange-300 underline-offset-2" href="#">Terms of Service</a> and <a class="text-orange-800 font-bold underline decoration-orange-300 underline-offset-2" href="#">Privacy Policy</a>.
                    </label>
                </div>

                <button class="w-full rounded-2xl bg-stone-900 py-4 font-bold text-stone-50 shadow-xl shadow-stone-900/20 hover:bg-orange-800 transition-all active:scale-[0.98] uppercase tracking-widest text-sm" type="submit">
                    Create Account
                </button>

                <p class="text-center text-sm text-stone-500 pt-4 font-medium">
                    Already have an account? <a class="font-bold text-orange-800 hover:text-orange-900 underline decoration-orange-200 underline-offset-4" href="{{ route('login') }}">Log In</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
