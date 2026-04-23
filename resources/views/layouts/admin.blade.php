<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'AnthroConnect Archivist Portal' }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Newsreader:ital,wght@0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "tertiary-fixed": "#feddb3",
                        "surface-container-low": "#f6f3f2",
                        "inverse-surface": "#303030",
                        "inverse-on-surface": "#f3f0f0",
                        "outline-variant": "#c5c8b8",
                        "tertiary": "#5e4829",
                        "secondary-container": "#f9dbb7",
                        "on-secondary-fixed-variant": "#574329",
                        "outline": "#75796b",
                        "primary-fixed": "#d2eca2",
                        "on-tertiary-container": "#fddcb2",
                        "on-secondary-fixed": "#281903",
                        "background": "#fbf9f8",
                        "on-surface": "#1b1c1c",
                        "surface-container-highest": "#e4e2e1",
                        "on-primary-fixed": "#131f00",
                        "surface-tint": "#50652a",
                        "on-background": "#1b1c1c",
                        "inverse-primary": "#b6d088",
                        "secondary": "#715b3e",
                        "surface-variant": "#e4e2e1",
                        "on-tertiary-fixed": "#281801",
                        "surface-container": "#f0eded",
                        "surface-dim": "#dcd9d9",
                        "secondary-fixed": "#fcdeba",
                        "on-secondary": "#ffffff",
                        "primary-container": "#556b2f",
                        "error-container": "#ffdad6",
                        "surface": "#fbf9f8",
                        "surface-bright": "#fbf9f8",
                        "on-error": "#ffffff",
                        "tertiary-container": "#78603e",
                        "on-primary-fixed-variant": "#394d14",
                        "secondary-fixed-dim": "#dfc29f",
                        "on-primary-container": "#d0eba1",
                        "surface-container-high": "#eae8e7",
                        "on-surface-variant": "#45483c",
                        "on-tertiary": "#ffffff",
                        "error": "#ba1a1a",
                        "on-error-container": "#93000a",
                        "on-primary": "#ffffff",
                        "primary-fixed-dim": "#b6d088",
                        "on-tertiary-fixed-variant": "#584324",
                        "primary": "#3e5219",
                        "tertiary-fixed-dim": "#e1c299",
                        "on-secondary-container": "#755f42",
                        "surface-container-lowest": "#ffffff"
                    },
                    borderRadius: {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    fontFamily: {
                        "headline": ["Newsreader"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .serif-italic { font-family: 'Newsreader', serif; font-style: italic; }
        .text-on-surface { color: #1b1c1c; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        [x-cloak] { display: none !important; }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="bg-surface font-body text-on-surface overflow-hidden" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        <!-- SideNavBar -->
        <aside class="bg-stone-100 dark:bg-stone-900 h-screen w-64 docked left-0 flex flex-col h-full py-6 px-4 shrink-0 overflow-y-auto no-scrollbar transition-all duration-300" 
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'">
            <div class="mb-10 px-2 flex items-center justify-between">
                <div x-show="sidebarOpen">
                    <h1 class="font-serif text-2xl font-bold text-primary dark:text-stone-100 italic">AnthroConnect</h1>
                    <p class="font-sans Inter tracking-tight text-xs text-stone-500 uppercase tracking-widest mt-1">Archivist Portal</p>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-stone-500">
                    <span class="material-symbols-outlined">menu_open</span>
                </button>
            </div>
            
            <nav class="flex-1 space-y-1">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'bg-primary text-on-primary shadow-sm dark:bg-primary/80' : 'text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800' }} rounded-sm font-medium flex items-center px-3 py-2.5 transition-all group" href="{{ route('admin.dashboard') }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">dashboard</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Dashboard</span>
                </a>
                <a class="{{ request()->routeIs('admin.onboarding.*') ? 'bg-primary text-on-primary shadow-sm dark:bg-primary/80' : 'text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800' }} rounded-sm font-medium flex items-center px-3 py-2.5 transition-all group" href="{{ route('admin.onboarding.index') }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">account_tree</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Onboarding Flows</span>
                </a>
                <a class="{{ request()->routeIs('admin.membership.*') ? 'bg-primary text-on-primary shadow-sm dark:bg-primary/80' : 'text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800' }} rounded-sm font-medium flex items-center px-3 py-2.5 transition-all group" href="{{ route('admin.membership.index') }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">workspace_premium</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Membership</span>
                </a>
                <a class="{{ request()->routeIs('admin.explore.*') ? 'bg-primary text-on-primary shadow-sm dark:bg-primary/80' : 'text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800' }} rounded-sm font-medium flex items-center px-3 py-2.5 transition-all group" href="{{ route('admin.explore.index') }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">explore</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Explore Content</span>
                </a>
                <a class="{{ request()->routeIs('admin.topics.*') ? 'bg-primary text-on-primary shadow-sm dark:bg-primary/80' : 'text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800' }} rounded-sm font-medium flex items-center px-3 py-2.5 transition-all group" href="{{ route('admin.topics.index') }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">category</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Topics</span>
                </a>

                <div x-data="{ lmsOpen: {{ request()->routeIs('admin.lms.*') ? 'true' : 'false' }} }" class="group w-full">
                    <button @click="lmsOpen = !lmsOpen" class="{{ request()->routeIs('admin.lms.*') ? 'bg-primary/10 text-primary' : 'text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800' }} rounded-sm font-medium flex items-center justify-between w-full px-3 py-2.5 transition-all">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined mr-3 text-[20px]">school</span>
                            <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">LMS</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="lmsOpen ? 'rotate-180' : ''" x-show="sidebarOpen">expand_more</span>
                    </button>
                    <div x-show="lmsOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-3 pb-2 pt-1 space-y-1">
                        <a href="{{ route('admin.lms.modules.index') }}" class="{{ request()->routeIs('admin.lms.modules.*') ? 'text-primary font-bold' : 'text-stone-500 hover:text-primary' }} block py-1.5 text-[11px] uppercase tracking-widest transition-colors font-semibold">Modules</a>
                        <a href="{{ route('admin.lms.resources.index') }}" class="{{ request()->routeIs('admin.lms.resources.*') ? 'text-primary font-bold' : 'text-stone-500 hover:text-primary' }} block py-1.5 text-[11px] uppercase tracking-widest transition-colors font-semibold">Resources</a>
                    </div>
                </div>
                
                <div x-data="{ encyOpen: {{ request()->routeIs('admin.encyclopedia.*') ? 'true' : 'false' }} }" class="group w-full">
                    <button @click="encyOpen = !encyOpen" class="{{ request()->routeIs('admin.encyclopedia.*') ? 'bg-primary/10 text-primary' : 'text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800' }} rounded-sm font-medium flex items-center justify-between w-full px-3 py-2.5 transition-all">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined mr-3 text-[20px]">local_library</span>
                            <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Encyclopedia</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="encyOpen ? 'rotate-180' : ''" x-show="sidebarOpen">expand_more</span>
                    </button>
                    <div x-show="encyOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-3 pb-2 pt-1 space-y-1">
                        <a href="{{ route('admin.encyclopedia.anthropologists.index') }}" class="{{ request()->routeIs('admin.encyclopedia.anthropologists.*') ? 'text-primary font-bold' : 'text-stone-500 hover:text-primary' }} block py-1.5 text-[11px] uppercase tracking-widest transition-colors font-semibold">Anthropologists</a>
                        <a href="{{ route('admin.encyclopedia.core-concepts.index') }}" class="{{ request()->routeIs('admin.encyclopedia.core-concepts.*') ? 'text-primary font-bold' : 'text-stone-500 hover:text-primary' }} block py-1.5 text-[11px] uppercase tracking-widest transition-colors font-semibold">Core Concepts</a>
                        <a href="{{ route('admin.encyclopedia.major-theories.index') }}" class="{{ request()->routeIs('admin.encyclopedia.major-theories.*') ? 'text-primary font-bold' : 'text-stone-500 hover:text-primary' }} block py-1.5 text-[11px] uppercase tracking-widest transition-colors font-semibold">Major Theories</a>
                    </div>
                </div>

                <a class="text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800 transition-colors flex items-center px-3 py-2.5" href="#">
                    <span class="material-symbols-outlined mr-3 text-[20px]">group</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">User Management</span>
                </a>
                        <div class="px-3 py-2">
                            <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-2 px-3" x-show="sidebarOpen">Community</p>
                            <a class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.community.topics.*') ? 'bg-primary/10 text-primary font-bold' : 'text-stone-600 hover:bg-stone-100' }}" 
                               href="{{ route('admin.community.topics.index') }}">
                                <span class="material-symbols-outlined mr-3 text-[20px]">category</span>
                                <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Manage Topics</span>
                            </a>
                            <a class="flex items-center px-3 py-2.5 rounded-lg mt-1 transition-colors {{ request()->routeIs('admin.community.discussions.*') ? 'bg-primary/10 text-primary font-bold' : 'text-stone-600 hover:bg-stone-100' }}" 
                               href="{{ route('admin.community.discussions.index') }}">
                                <span class="material-symbols-outlined mr-3 text-[20px]">forum</span>
                                <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Discussions</span>
                            </a>
                        </div>
                <a class="text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800 transition-colors flex items-center px-3 py-2.5" href="#">
                    <span class="material-symbols-outlined mr-3 text-[20px]">school</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">UPSC Hub</span>
                </a>
                <div x-data="{ libOpen: {{ request()->routeIs('admin.library.*') ? 'true' : 'false' }} }" class="group w-full">
                    <button @click="libOpen = !libOpen" class="{{ request()->routeIs('admin.library.*') ? 'bg-primary/10 text-primary' : 'text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800' }} rounded-sm font-medium flex items-center justify-between w-full px-3 py-2.5 transition-all">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined mr-3 text-[20px]">library_books</span>
                            <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Research Library</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="libOpen ? 'rotate-180' : ''" x-show="sidebarOpen">expand_more</span>
                    </button>
                    <div x-show="libOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-3 pb-2 pt-1 space-y-1">
                        <a href="{{ route('admin.library.dashboard') }}" class="{{ request()->routeIs('admin.library.dashboard') ? 'text-primary font-bold' : 'text-stone-500 hover:text-primary' }} block py-1.5 text-[11px] uppercase tracking-widest transition-colors font-semibold">Overview</a>
                        <a href="{{ route('admin.library.resources.index') }}" class="{{ request()->routeIs('admin.library.resources.*') ? 'text-primary font-bold' : 'text-stone-500 hover:text-primary' }} block py-1.5 text-[11px] uppercase tracking-widest transition-colors font-semibold">Resources</a>
                        <a href="{{ route('admin.library.resource-types.index') }}" class="{{ request()->routeIs('admin.library.resource-types.*') ? 'text-primary font-bold' : 'text-stone-500 hover:text-primary' }} block py-1.5 text-[11px] uppercase tracking-widest transition-colors font-semibold">Resource Types</a>
                    </div>
                </div>

                <a class="text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800 transition-colors flex items-center px-3 py-2.5" href="#">
                    <span class="material-symbols-outlined mr-3 text-[20px]">monitoring</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Analytics</span>
                </a>
                <a class="text-stone-600 dark:text-stone-400 hover:text-primary hover:bg-stone-200 dark:hover:bg-stone-800 transition-colors flex items-center px-3 py-2.5" href="#">
                    <span class="material-symbols-outlined mr-3 text-[20px]">settings</span>
                    <span class="font-sans Inter tracking-tight" x-show="sidebarOpen">Settings</span>
                </a>
            </nav>
            
            <div class="mt-auto pt-6 border-t border-stone-200/50 flex items-center space-x-3">
                <img alt="Admin Avatar" class="w-10 h-10 rounded-lg object-cover" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.Auth::user()->name }}"/>
                <div x-show="sidebarOpen">
                    <p class="text-sm font-semibold text-stone-900">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] uppercase tracking-tighter text-stone-500">{{ Auth::user()->getRoleNames()->first() ?? 'Archivist' }}</p>
                    <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="text-[10px] text-error font-bold hover:underline">LOGOUT</button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Content Area -->
        <main class="flex-1 flex flex-col min-w-0 bg-surface overflow-hidden">
            <!-- TopAppBar -->
            <header class="flex justify-between items-center px-8 h-16 sticky top-0 z-40 bg-stone-50/70 dark:bg-stone-950/70 backdrop-blur-xl shadow-sm shadow-stone-200/50 dark:shadow-none">
                <div class="flex items-center flex-1 max-w-lg">
                    <button @click="sidebarOpen = !sidebarOpen" class="mr-4 text-stone-500 hidden lg:block">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <div class="relative w-full">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                        <input class="w-full bg-surface-container-lowest border-none rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary" placeholder="Search archive, research, or users..." type="text"/>
                    </div>
                </div>
                <div class="flex items-center space-x-6 ml-6">
                    <button class="text-stone-500 hover:text-primary transition-all flex items-center">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button class="text-stone-500 hover:text-primary transition-all flex items-center">
                        <span class="material-symbols-outlined">history_edu</span>
                    </button>
                    <a class="text-stone-500 hover:text-olive-700 transition-all text-sm font-sans Inter" href="#">Support</a>
                    <div class="h-8 w-[1px] bg-stone-200"></div>
                    <a href="{{ route('profile.edit') }}">
                        <img alt="Administrator Profile" class="w-8 h-8 rounded-full object-cover" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.Auth::user()->name }}"/>
                    </a>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8 space-y-8 no-scrollbar">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    @livewireScripts
    @stack('scripts')
    <x-delete-confirm-modal />
</body>
</html>
