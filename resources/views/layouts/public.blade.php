<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>AnthroConnect - Understanding Humanity</title>

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

<!-- Alpine.js for Drawer Logic handled by Livewire 3+ -->
{{-- 
@if(!isset($noManualAlpine))
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endif
--}}

@livewireStyles

<script>
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
              stone: {
                50: "#fafaf9", 100: "#f5f5f4", 200: "#e7e5e4", 300: "#d6d3d1", 400: "#a8a29e",
                500: "#78716c", 600: "#57534e", 700: "#44403c", 800: "#292524", 900: "#1c1917", 950: "#0c0a09",
              },
              orange: {
                50: "#fffaf0", 100: "#ffedd5", 200: "#fed7aa", 300: "#fdba74",
                700: "#c2410c", 800: "#9a3412", 900: "#7c2d12",
              },
              primary: "#9a3412",
              olive: "#606c38",
              sand: "#fdf6e3",
            },
            fontFamily: {
              headline: ["Lora", "serif"],
              body: ["Public Sans", "sans-serif"],
              serif: ["Lora", "serif"],
              sans: ["Public Sans", "sans-serif"],
              "serif-heading": ["Lora", "serif"],
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
        },
    },
}
</script>

<style>
[x-cloak] { display: none !important; }
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
.ethno-pattern {
    background-image: radial-gradient(circle at 2px 2px, rgba(158, 80, 21, 0.05) 1px, transparent 0);
    background-size: 24px 24px;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@stack('styles')
</head>

<body class="bg-stone-50 text-stone-900 font-sans antialiased" x-data="{ drawerOpen: false }">

<!-- Header -->
<header class="bg-stone-50/95 dark:bg-stone-950/95 backdrop-blur-md text-orange-900 dark:text-orange-200 fixed top-0 w-full z-50 border-b border-stone-200 dark:border-stone-800 shadow-sm h-16 w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex justify-between items-center">
        @if(isset($simpleMode) && $simpleMode)
            <!-- Simple Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                <span class="material-symbols-outlined text-stone-900 dark:text-stone-50">account_balance</span>
                <h1 class="text-xl font-headline italic text-stone-900 dark:text-stone-50 tracking-tight">AnthroConnect</h1>
            </a>
            <a href="{{ route('login') }}" class="text-stone-500 text-sm font-medium px-3 py-2 rounded-lg hover:bg-stone-100 transition-colors">Log In</a>
        @else
            <!-- Mobile Menu Toggle -->
            <button @click="drawerOpen = true" class="lg:hidden active:scale-95 duration-150 p-2 rounded-full hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <!-- Brand -->
            <a wire:navigate href="{{ route('home') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                <h1 class="text-2xl font-headline italic text-stone-900 dark:text-stone-50 tracking-tight">AnthroConnect</h1>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-8 lg:ml-12">
                <a wire:navigate class="text-sm font-medium transition-colors {{ request()->routeIs('modules.*') ? 'text-orange-800 font-bold border-b-2 border-orange-800 pb-1' : 'hover:text-orange-700' }}" href="{{ route('modules.index') }}">Learn Anthropology</a>
                <a wire:navigate class="text-sm font-medium transition-colors {{ request()->routeIs('explore.*') ? 'text-orange-800 font-bold border-b-2 border-orange-800 pb-1' : 'hover:text-orange-700' }}" href="{{ route('explore.index') }}">Explore Humanity</a>
                <a wire:navigate class="text-sm font-medium transition-colors {{ request()->routeIs('knowledge-map.show') ? 'text-orange-800 font-bold border-b-2 border-orange-800 pb-1' : 'hover:text-orange-700' }}" href="{{ route('knowledge-map.show') }}">Knowledge Map</a>
                <a class="text-sm font-medium transition-colors {{ request()->routeIs('upsc.hub') ? 'text-orange-800 font-bold border-b-2 border-orange-800 pb-1' : 'hover:text-orange-700' }}" href="{{ route('upsc.hub') }}">UPSC Anthropology</a>
                <a wire:navigate class="text-sm font-medium transition-colors {{ request()->routeIs('encyclopedia.*') ? 'text-orange-800 font-bold border-b-2 border-orange-800 pb-1' : 'hover:text-orange-700' }}" href="{{ route('encyclopedia.index') }}">Encyclopedia</a>
                <a wire:navigate class="text-sm font-medium transition-colors {{ request()->routeIs('community.*') ? 'text-orange-800 font-bold border-b-2 border-orange-800 pb-1' : 'hover:text-orange-700' }}" href="{{ route('community.index') }}">Community</a>
                <a wire:navigate class="text-sm font-medium transition-colors {{ request()->routeIs('exams.*') ? 'text-orange-800 font-bold border-b-2 border-orange-800 pb-1' : 'hover:text-orange-700' }}" href="{{ route('exams.index') }}">Practice Exams</a>
                <a wire:navigate class="text-sm font-medium transition-colors {{ request()->routeIs('library.*') ? 'text-orange-800 font-bold border-b-2 border-orange-800 pb-1' : 'hover:text-orange-700' }}" href="{{ route('library.index') }}">Research Library</a>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-2 sm:gap-4">
                
                @auth
                    <div class="flex items-center gap-3">
                        <a wire:navigate href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                            <span class="text-sm font-semibold text-stone-700 hidden sm:inline-block">Hi, {{ explode(' ', Auth::user()->name)[0] }}</span>
                            <div class="h-8 w-8 rounded-full overflow-hidden border border-stone-200">
                                <img alt="User profile" class="h-full w-full object-cover" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.Auth::user()->name }}"/>
                            </div>
                        </a>
                        <div class="h-4 w-px bg-stone-200"></div>
                        <form method="POST" action="{{ route('logout') }}" id="header-logout-form" class="hidden">@csrf</form>
                        <button onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();" class="text-[10px] font-bold text-stone-400 uppercase tracking-widest hover:text-orange-800 transition-colors">Logout</button>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2">
                        <a wire:navigate href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold hover:text-orange-700">Login</a>
                        <a wire:navigate href="{{ route('register') }}" class="bg-stone-900 text-stone-50 px-5 py-2 rounded-xl text-sm font-bold shadow-lg shadow-stone-900/10 hover:-translate-y-0.5 transition-all">Sign Up</a>
                    </div>
                @endauth
            </div>
        @endif
    </div>
</header>

<!-- Mobile Navigation Drawer -->
@if(!isset($simpleMode) || !$simpleMode)
<div class="fixed inset-0 z-[100]" x-show="drawerOpen" x-cloak>
    <!-- Overlay -->
    <div class="fixed inset-0 bg-stone-900/50 backdrop-blur-sm transition-opacity" x-show="drawerOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="drawerOpen = false"></div>
    
    <!-- Drawer Panel -->
    <div class="fixed inset-y-0 left-0 w-80 bg-stone-50 dark:bg-stone-900 flex flex-col p-8 shadow-2xl rounded-r-3xl h-full transition-transform" x-show="drawerOpen" x-transition:enter="ease-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
        <div class="flex justify-between items-center mb-10">
            <div class="text-3xl font-headline italic text-stone-900 dark:text-stone-50">AnthroConnect</div>
            <button @click="drawerOpen = false" class="text-stone-400 hover:text-stone-900"><span class="material-symbols-outlined">close</span></button>
        </div>
        
        <nav class="flex flex-col space-y-2">
            <a wire:navigate class="flex items-center gap-4 py-4 px-4 {{ request()->routeIs('explore.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-900 dark:text-orange-100 font-bold' : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100' }} rounded-xl transition-all" href="{{ route('explore.index') }}">
                <span class="material-symbols-outlined">explore</span>
                <span class="font-headline text-lg">Explore Humanity</span>
            </a>
            <a wire:navigate class="flex items-center gap-4 py-4 px-4 {{ request()->routeIs('modules.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-900 dark:text-orange-100 font-bold' : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100' }} rounded-xl transition-all" href="{{ route('modules.index') }}">
                <span class="material-symbols-outlined">school</span>
                <span class="font-headline text-lg">Learn Anthropology</span>
            </a>
            <a wire:navigate class="flex items-center gap-4 py-4 px-4 {{ request()->routeIs('knowledge-map.show') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-900 dark:text-orange-100 font-bold' : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100' }} rounded-xl transition-all" href="{{ route('knowledge-map.show') }}">
                <span class="material-symbols-outlined">hub</span>
                <span class="font-headline text-lg">Knowledge Map</span>
            </a>
            <a wire:navigate class="flex items-center gap-4 py-4 px-4 {{ request()->routeIs('upsc.hub') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-900 dark:text-orange-100 font-bold' : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100' }} rounded-xl transition-all" href="{{ route('upsc.hub') }}">
                <span class="material-symbols-outlined">account_balance</span>
                <span class="font-headline text-lg">UPSC Anthropology</span>
            </a>
            <a wire:navigate class="flex items-center gap-4 py-4 px-4 {{ request()->routeIs('encyclopedia.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-900 dark:text-orange-100 font-bold' : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100' }} rounded-xl transition-all" href="{{ route('encyclopedia.index') }}">
                <span class="material-symbols-outlined">account_tree</span>
                <span class="font-headline text-lg">Encyclopedia</span>
            </a>
            <a wire:navigate class="flex items-center gap-4 py-4 px-4 {{ request()->routeIs('community.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-900 dark:text-orange-100 font-bold' : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100' }} rounded-xl transition-all" href="{{ route('community.index') }}">
                <span class="material-symbols-outlined">forum</span>
                <span class="font-headline text-lg">Community</span>
            </a>
            <a wire:navigate class="flex items-center gap-4 py-4 px-4 {{ request()->routeIs('exams.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-900 dark:text-orange-100 font-bold' : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100' }} rounded-xl transition-all" href="{{ route('exams.index') }}">
                <span class="material-symbols-outlined">edit_note</span>
                <span class="font-headline text-lg">Practice Exams</span>
            </a>
            <a wire:navigate class="flex items-center gap-4 py-4 px-4 {{ request()->routeIs('library.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-900 dark:text-orange-100 font-bold' : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100' }} rounded-xl transition-all" href="{{ route('library.index') }}">
                <span class="material-symbols-outlined">library_books</span>
                <span class="font-headline text-lg">Research Library</span>
            </a>

            @auth
                <div class="pt-6 mt-6 border-t border-stone-100 flex flex-col gap-2">
                    <div class="flex items-center gap-3 px-4 mb-4">
                        <div class="h-10 w-10 rounded-full overflow-hidden border border-stone-200">
                            <img alt="User profile" class="h-full w-full object-cover" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.Auth::user()->name }}"/>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-stone-900">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-stone-500 uppercase tracking-widest">Logged In</p>
                        </div>
                    </div>
                    
                    <a wire:navigate class="flex items-center gap-4 py-4 px-4 text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 rounded-xl transition-all" href="{{ route('dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="font-headline text-lg">Dashboard</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" id="drawer-logout-form" class="hidden">@csrf</form>
                    <button onclick="event.preventDefault(); document.getElementById('drawer-logout-form').submit();" class="flex items-center gap-4 py-4 px-4 text-orange-800 hover:bg-orange-50 rounded-xl transition-all w-full text-left">
                        <span class="material-symbols-outlined">logout</span>
                        <span class="font-headline text-lg">Logout</span>
                    </button>
                </div>
            @endauth

            @guest
                <div class="pt-6 mt-6 border-t border-stone-100 flex flex-col gap-4">
                    <a wire:navigate href="{{ route('login') }}" class="w-full py-4 text-center font-bold text-stone-900">Login</a>
                    <a wire:navigate href="{{ route('register') }}" class="w-full py-4 bg-stone-900 text-stone-50 text-center font-bold rounded-xl shadow-lg shadow-stone-900/20">Sign Up</a>
                </div>
            @endguest
        </nav>
    </div>
</div>
@endif

<!-- Page Content -->
<main class="{{ (isset($simpleMode) && $simpleMode) ? '' : 'pt-16' }}">
    @yield('content')
    {{ $slot ?? '' }}
</main>

<!-- Footer -->
<footer class="bg-stone-100 dark:bg-stone-950 text-stone-600 dark:text-stone-400 w-full py-16 border-t border-stone-200 dark:border-stone-800 px-8">
    <div class="max-w-7xl mx-auto flex flex-col items-center gap-10">
        @if(isset($simpleMode) && $simpleMode)
            <div class="flex justify-center gap-6 text-xs font-bold uppercase tracking-widest">
                <a class="hover:text-orange-700 transition-all" href="#">Terms of Service</a>
                <span class="text-stone-300">•</span>
                <a class="hover:text-orange-700 transition-all" href="#">Privacy Policy</a>
            </div>
        @else
            <div class="w-full max-w-sm">
                <h5 class="font-bold text-stone-900 dark:text-stone-100 mb-4 text-xs uppercase tracking-widest text-center">Stay Informed</h5>
                <div class="flex gap-2">
                    <input class="flex-1 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-orange-800 outline-none" placeholder="Email address" type="email"/>
                    <button class="bg-stone-900 dark:bg-orange-800 text-stone-50 px-4 py-3 rounded-xl shadow-lg hover:-translate-y-0.5 transition"><span class="material-symbols-outlined">send</span></button>
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-8 font-sans text-xs uppercase tracking-widest font-bold">
                <a class="hover:text-orange-700 transition-all" href="#">Ethics</a>
                <a class="hover:text-orange-700 transition-all" href="#">Research</a>
                <a class="hover:text-orange-700 transition-all" href="#">Archive</a>
                <a class="hover:text-orange-700 transition-all underline decoration-orange-300 underline-offset-4" href="#">Contact</a>
            </div>
        @endif
        
        <div class="text-[10px] text-stone-500 uppercase tracking-widest font-bold text-center">
            © 2024 AnthroConnect Platform. Preserving human narratives globally.
        </div>
    </div>
</footer>

    <livewire:public.upgrade-modal />
    <livewire:public.community.start-discussion-modal />
    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            // Handle session expiration gracefully
            Livewire.on('session.expired', () => {
                window.location.reload();
            });

            // Cleanup any leftover modal backdrops on navigation
            document.addEventListener('livewire:navigated', () => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        });
        
        // Fallback for non-livewire navigations / initial load
        window.addEventListener('load', () => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length > 0) {
                backdrops.forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
