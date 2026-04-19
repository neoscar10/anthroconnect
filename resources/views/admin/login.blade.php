<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Archivist Portal Access | The Tactile Archivist</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "outline": "#75796b",
                        "secondary-container": "#f9dbb7",
                        "on-primary-fixed": "#131f00",
                        "on-tertiary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "primary-fixed": "#d2eca2",
                        "on-primary-fixed-variant": "#394d14",
                        "surface": "#fbf9f8",
                        "on-secondary-container": "#755f42",
                        "surface-container-highest": "#e4e2e1",
                        "tertiary-fixed-dim": "#e1c299",
                        "on-secondary-fixed": "#281903",
                        "error-container": "#ffdad6",
                        "surface-container-high": "#eae8e7",
                        "secondary": "#715b3e",
                        "secondary-fixed": "#fcdeba",
                        "on-tertiary-fixed": "#281801",
                        "primary": "#3e5219",
                        "surface-dim": "#dcd9d9",
                        "surface-variant": "#e4e2e1",
                        "on-surface-variant": "#45483c",
                        "inverse-primary": "#b6d088",
                        "tertiary-fixed": "#feddb3",
                        "surface-container": "#f0eded",
                        "on-tertiary-container": "#fddcb2",
                        "inverse-surface": "#303030",
                        "surface-tint": "#50652a",
                        "outline-variant": "#c5c8b8",
                        "tertiary-container": "#78603e",
                        "tertiary": "#5e4829",
                        "secondary-fixed-dim": "#dfc29f",
                        "on-background": "#1b1c1c",
                        "surface-container-low": "#f6f3f2",
                        "on-secondary": "#ffffff",
                        "primary-fixed-dim": "#b6d088",
                        "on-primary": "#ffffff",
                        "inverse-on-surface": "#f3f0f0",
                        "background": "#fbf9f8",
                        "surface-bright": "#fbf9f8",
                        "on-error": "#ffffff",
                        "error": "#ba1a1a",
                        "on-surface": "#1b1c1c",
                        "on-primary-container": "#d0eba1",
                        "primary-container": "#556b2f",
                        "on-tertiary-fixed-variant": "#584324",
                        "on-error-container": "#93000a",
                        "on-secondary-fixed-variant": "#574329"
                    },
                    borderRadius: {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    fontFamily: {
                        "headline": ["Newsreader", "serif"],
                        "body": ["Inter", "sans-serif"],
                        "label": ["Inter", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .serif-italic { font-family: 'Newsreader', serif; font-style: italic; }
        .ambient-shadow {
            box-shadow: 0px 4px 20px rgba(27, 28, 28, 0.04), 0px 8px 40px rgba(27, 28, 28, 0.08);
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface min-h-screen flex flex-col">
    <!-- TopNavBar -->
    <header class="bg-[#fbf9f8] dark:bg-stone-950/70 text-[#3e5219] dark:text-[#c5c8b8] sticky top-0 z-50 backdrop-blur-md">
        <div class="flex justify-between items-center px-8 py-6 w-full">
            <div class="text-xl font-headline italic text-[#3e5219] dark:text-[#fbf9f8]">
                The Tactile Archivist
            </div>
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined text-outline">help_outline</span>
            </div>
        </div>
    </header>

    <!-- Main Content: Login Journey -->
    <main class="flex-grow flex items-center justify-center px-6 py-12 relative overflow-hidden">
        <!-- Subtle Texture Background -->
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
            <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[60%] bg-primary-fixed-dim rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[50%] bg-tertiary-fixed rounded-full blur-[100px]"></div>
        </div>
        
        <div class="z-10 w-full max-w-md">
            <!-- Centered Login Card -->
            <div class="bg-surface-container-lowest ambient-shadow rounded-xl p-10 border border-outline-variant/10">
                <!-- Brand Header -->
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center p-3 rounded-xl bg-surface-container-high mb-6">
                        <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
                    </div>
                    <h1 class="text-3xl font-headline font-semibold text-on-surface mb-2">Archivist Portal Access</h1>
                    <p class="text-on-surface-variant text-sm px-4">
                        Enter your credentials to manage the anthropology repository.
                    </p>
                </div>

                <!-- Login Form -->
                <form class="space-y-6" method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-label uppercase tracking-widest text-on-surface-variant ml-1" for="email">Admin Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </div>
                            <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-low border-0 border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-lg text-on-surface placeholder:text-outline/40 transition-all text-sm @error('email') border-error @enderror" 
                                id="email" name="email" value="{{ old('email') }}" placeholder="archivist@institution.edu" required autofocus type="email"/>
                        </div>
                        @error('email') <p class="text-[10px] text-error mt-1 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center px-1">
                            <label class="block text-[11px] font-label uppercase tracking-widest text-on-surface-variant" for="password">Security Key</label>
                            <a class="text-[11px] font-label uppercase tracking-widest text-primary hover:underline transition-all" href="#">Forgot?</a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-outline group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </div>
                            <input class="block w-full pl-11 pr-4 py-3 bg-surface-container-low border-0 border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-lg text-on-surface placeholder:text-outline/40 transition-all text-sm" 
                                id="password" name="password" placeholder="••••••••••••" required type="password"/>
                        </div>
                        @error('password') <p class="text-[10px] text-error mt-1 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center px-1">
                        <input class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary focus:ring-offset-surface" id="remember" name="remember" type="checkbox"/>
                        <label class="ml-2 block text-xs text-on-surface-variant" for="remember">Remember this station</label>
                    </div>

                    <button class="w-full py-4 px-6 bg-gradient-to-br from-primary to-primary-container text-white font-label text-sm uppercase tracking-widest font-bold rounded-lg ambient-shadow hover:scale-[1.01] active:scale-[0.98] transition-all flex items-center justify-center gap-2" type="submit">
                        <span>Access Portal</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </form>

                <!-- Security Notice -->
                <div class="mt-8 pt-8 border-t border-outline-variant/10 text-center">
                    <p class="text-[10px] text-on-surface-variant/60 italic leading-relaxed">
                        <span class="material-symbols-outlined text-[12px] align-text-bottom mr-1">security</span>
                        Unauthorized access is strictly prohibited and monitored.<br/>
                        All archival retrieval activities are timestamped and logged.
                    </p>
                </div>
            </div>

            <!-- Contextual Link (Return Home) -->
            <div class="mt-8 text-center">
                <a class="inline-flex items-center gap-2 text-xs text-on-surface-variant hover:text-primary transition-colors group" href="{{ route('home') }}">
                    <span class="material-symbols-outlined text-[16px] group-hover:-translate-x-1 transition-transform">west</span>
                    Return to Public Repository
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#fbf9f8] dark:bg-stone-950 border-t border-[#c5c8b8]/20 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center px-12 w-full max-w-7xl mx-auto">
            <div class="font-sans text-[11px] uppercase tracking-widest text-[#1b1c1c]/50 mb-4 md:mb-0">
                © 2024 The Tactile Archivist. Institutional Access Only.
            </div>
            <div class="flex gap-8">
                <a class="font-sans text-[11px] uppercase tracking-widest text-stone-500 hover:text-[#3e5219] dark:hover:text-white transition-all underline-offset-4 hover:underline" href="#">Privacy Policy</a>
                <a class="font-sans text-[11px] uppercase tracking-widest text-stone-500 hover:text-[#3e5219] dark:hover:text-white transition-all underline-offset-4 hover:underline" href="#">Terms of Service</a>
                <a class="font-sans text-[11px] uppercase tracking-widest text-stone-500 hover:text-[#3e5219] dark:hover:text-white transition-all underline-offset-4 hover:underline" href="#">Security Protocol</a>
            </div>
        </div>
    </footer>
</body>
</html>
