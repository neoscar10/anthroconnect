<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 md:hidden">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
            <input
                class="w-full bg-sand/30 border-none rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-primary/50"
                placeholder="Search resources..."
                type="text"
            />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-9 space-y-12">
            <!-- Hero -->
            <section class="relative overflow-hidden rounded-xl bg-primary text-white p-6 sm:p-8 md:p-12">
                <div class="relative z-10 max-w-2xl">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4">
                        Welcome Back, {{ $this->userFirstName }}
                    </h1>
                    <p class="text-base sm:text-lg opacity-90 mb-8 font-display">
                        Continue exploring the depths of anthropology and humanity through our curated knowledge base and academic community.
                    </p>

                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:gap-4">
                        <button class="bg-white text-primary px-6 py-3 rounded-lg font-bold text-sm flex items-center justify-center gap-2 hover:bg-sand transition-colors">
                            <span class="material-symbols-outlined text-sm">play_circle</span>
                            Continue Learning
                        </button>

                        <button class="bg-primary/20 backdrop-blur-md border border-white/30 text-white px-6 py-3 rounded-lg font-bold text-sm flex items-center justify-center gap-2 hover:bg-white/10 transition-colors">
                            <span class="material-symbols-outlined text-sm">map</span>
                            Explore Knowledge Map
                        </button>

                        <button class="bg-white/10 text-white px-6 py-3 rounded-lg font-bold text-sm flex items-center justify-center gap-2 hover:bg-white/20 transition-colors">
                            <span class="material-symbols-outlined text-sm">group</span>
                            Join Community
                        </button>
                    </div>
                </div>

                <div class="absolute right-0 top-0 h-full w-1/3 opacity-20 pointer-events-none bg-gradient-to-l from-black/40 to-transparent">
                    <span class="material-symbols-outlined text-[120px] sm:text-[160px] md:text-[200px] absolute -right-6 sm:-right-8 md:-right-10 -bottom-8 rotate-12">history_edu</span>
                </div>
            </section>
            
            <!-- Membership Status Section -->
            <section class="mt-8 transition-all duration-500">
                @if($isMember)
                    <!-- Active Member View -->
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary/5 via-sand/20 to-white border border-primary/20 p-6 sm:p-8 flex flex-col md:flex-row items-center gap-6 shadow-sm group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <span class="material-symbols-outlined text-8xl text-primary">workspace_premium</span>
                        </div>
                        
                        <div class="shrink-0 flex items-center justify-center w-20 h-20 rounded-full bg-primary/10 border-4 border-white shadow-inner">
                            <span class="material-symbols-outlined text-4xl text-primary" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                        </div>
                        
                        <div class="flex-1 text-center md:text-left z-10">
                            <div class="flex flex-col md:flex-row md:items-center gap-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-primary text-white mx-auto md:mx-0">
                                    Verified Scholar
                                </span>
                                @if($userMembership->started_at)
                                    <span class="text-[10px] text-on-surface-variant font-medium opacity-60">Member since {{ $userMembership->started_at->format('M Y') }}</span>
                                @endif
                            </div>
                            <h3 class="text-2xl font-bold text-on-surface mb-2">You're an Active AnthroConnect Member</h3>
                            <p class="text-sm text-on-surface-variant max-w-xl">You have full access to our curated research library, UPSC hub case studies, and advanced community tools. Welcome to the inner circle.</p>
                        </div>

                        <div class="shrink-0 flex flex-col items-center md:items-end gap-3 z-10">
                            <div class="flex -space-x-1">
                                <div class="w-8 h-8 rounded-full bg-sand border-2 border-white flex items-center justify-center text-[8px] font-bold text-primary">LI</div>
                                <div class="w-8 h-8 rounded-full bg-primary border-2 border-white flex items-center justify-center text-[8px] font-bold text-white">RE</div>
                                <div class="w-8 h-8 rounded-full bg-olive border-2 border-white flex items-center justify-center text-[8px] font-bold text-white">UP</div>
                            </div>
                            <button class="bg-primary/5 text-primary border border-primary/10 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-primary hover:text-white transition-all">
                                View Benefits
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Non-Member View -->
                    <div class="relative overflow-hidden rounded-2xl bg-white border border-sand p-6 sm:p-8 flex flex-col md:flex-row items-center gap-8 shadow-sm group">
                        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:rotate-12 transition-transform duration-700">
                            <span class="material-symbols-outlined text-9xl text-slate-900">loyalty</span>
                        </div>
                        
                        <div class="flex-1 text-center md:text-left">
                            <div class="mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-sand text-primary mx-auto md:mx-0">
                                    Standard Experience
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold text-on-surface mb-2">Unlock the Full AnthroConnect Experience</h3>
                            <p class="text-sm text-on-surface-variant max-w-xl mb-6">Gain full access to our proprietary case study bank, previous year UPSC trends, and rare ethnographic field notes by becoming a member today.</p>
                            
                            @if($globalSetting)
                                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                                    @foreach($globalSetting->privileges->take(3) as $privilege)
                                        <div class="flex items-center gap-2 text-[11px] font-medium text-slate-600">
                                            <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                                            {{ $privilege->privilege }}
                                        </div>
                                    @endforeach
                                    @if($globalSetting->privileges->count() > 3)
                                        <span class="text-[11px] text-slate-400 font-medium italic">and more...</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="shrink-0 flex flex-col items-center gap-3">
                            <div class="text-center mb-1">
                                <span class="text-xs text-slate-400 block uppercase tracking-widest font-bold">Invest in growth</span>
                                @if($globalSetting)
                                    <span class="text-2xl font-headline italic font-bold text-primary">₹ {{ number_format($globalSetting->price_inr, 2) }}</span>
                                @else
                                    <span class="text-sm font-medium text-slate-400">Membership coming soon</span>
                                @endif
                            </div>
                            <button wire:click="openCheckout" class="bg-primary text-white px-8 py-3 rounded-xl font-bold text-sm uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                                Become a member
                            </button>
                        </div>
                    </div>
                @endif
            </section>

            <!-- Continue Learning -->
            <section>
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">school</span>
                    Continue Learning
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($continueLearning as $item)
                        <div class="bg-white border border-sand rounded-xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start gap-4 mb-4">
                                    <span class="bg-sand text-primary px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">
                                        {{ $item['tag'] }}
                                    </span>
                                    <span class="text-xs text-slate-500 shrink-0">
                                        {{ $item['progress'] }}% Complete
                                    </span>
                                </div>

                                <h3 class="text-xl font-bold mb-2">{{ $item['title'] }}</h3>
                                <p class="text-sm text-slate-600 mb-6">{{ $item['description'] }}</p>
                            </div>

                            <div class="w-full bg-sand rounded-full h-2 mb-4">
                                <div class="bg-primary h-2 rounded-full" style="width: {{ $item['progress'] }}%"></div>
                            </div>

                            <button class="text-primary text-sm font-bold flex items-center gap-1 hover:underline">
                                Resume Module
                                <span class="material-symbols-outlined text-xs">arrow_forward</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Recommended -->
            <section>
                <div class="flex justify-between items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold">Recommended for You</h2>
                    <a class="text-sm text-primary font-medium whitespace-nowrap" href="#">View All</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($recommendedItems as $item)
                        <div class="flex gap-4 p-4 bg-white border border-sand rounded-xl group cursor-pointer hover:border-primary transition-colors">
                            <div class="w-24 h-24 bg-sand rounded-lg shrink-0 overflow-hidden">
                                <img class="w-full h-full object-cover" src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
                            </div>

                            <div class="flex flex-col justify-center min-w-0">
                                <h4 class="font-bold text-lg group-hover:text-primary transition-colors">
                                    {{ $item['title'] }}
                                </h4>
                                <p class="text-sm text-slate-500">{{ $item['description'] }}</p>

                                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-slate-400">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">{{ $item['meta_left_icon'] }}</span>
                                        {{ $item['meta_left'] }}
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">{{ $item['meta_right_icon'] }}</span>
                                        {{ $item['meta_right'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Explore Humanity -->
            <section>
                <h2 class="text-2xl font-bold mb-6">Explore Humanity</h2>

                <div class="bg-sand/20 rounded-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
                    <div class="h-64 md:h-auto overflow-hidden">
                        <img
                            class="w-full h-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAdXPeFId6STTdfqjvR_7DOnmClBQwVMedR4giCsf1gejHnoeh3Lt4fzJ8e6tz_4SZ5JYnKann2Xc-qqzmE9KUmh9SKlthOJz-L1batBjJprKlltJ7vf0-OcwEalDCELeIJYbR_K3e5yxXvSRHivF_gjcIykzLqWZFrlXTxhrUYX03P_m4vDDq5yEy-Ts80MkDAghDkvmpnqURdls2K2JRTuBhyODgkyG3fK46xaI945IgjPM1ITA7qsJ7GGt4Dbx5VRzQ4nYEIRU8"
                            alt="The Anthropology of Food Traditions"
                        >
                    </div>

                    <div class="p-6 sm:p-8 flex flex-col justify-center">
                        <span class="text-primary font-bold text-xs uppercase tracking-widest mb-2">Long Read</span>
                        <h3 class="text-2xl sm:text-3xl font-bold mb-4 leading-tight">The Anthropology of Food Traditions</h3>
                        <p class="text-slate-600 mb-6 italic font-serif">
                            "Tell me what you eat, and I will tell you who you are." Explore how culinary rituals define cultural identity in the modern era.
                        </p>
                        <div class="flex flex-wrap items-center gap-4">
                            <button class="bg-primary text-white px-6 py-2 rounded font-bold text-sm">Read Article</button>
                            <span class="text-xs text-slate-500">15 min read</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Knowledge Map -->
            <section class="bg-olive/10 rounded-2xl p-6 sm:p-8 border border-olive/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold mb-4">Interconnected Knowledge Map</h2>
                        <p class="text-slate-700 mb-6">
                            Our proprietary visualization tool allows you to see the cross-disciplinary links between Biological, Social, and Linguistic anthropology.
                        </p>

                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                Visual relationship tracking
                            </li>
                            <li class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                Dynamic branch navigation
                            </li>
                            <li class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                Gap analysis for exam prep
                            </li>
                        </ul>

                        <button class="border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-2 rounded-lg font-bold transition-all">
                            Launch Map Viewer
                        </button>
                    </div>

                    <div class="bg-white/50 rounded-xl aspect-square flex items-center justify-center border border-olive/30 relative overflow-hidden">
                        <span class="material-symbols-outlined text-[100px] sm:text-[120px] text-olive/40">hub</span>
                        <div class="absolute top-1/4 left-1/4 h-3 w-3 bg-primary rounded-full animate-pulse"></div>
                        <div class="absolute bottom-1/3 right-1/4 h-3 w-3 bg-primary rounded-full animate-pulse"></div>
                        <div class="absolute top-1/2 right-1/2 h-3 w-3 bg-olive rounded-full"></div>
                    </div>
                </div>
            </section>

            <!-- Community Discussions -->
            <section>
                <div class="flex justify-between items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold">Community Discussions</h2>
                    <button class="text-primary font-medium text-sm whitespace-nowrap">New Topic</button>
                </div>

                <div class="space-y-4">
                    @foreach($discussions as $discussion)
                        <div class="bg-white p-5 rounded-xl border border-sand hover:shadow-md transition-shadow">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                                <h4 class="font-bold text-lg">{{ $discussion['title'] }}</h4>
                                <div class="flex items-center gap-1 text-slate-400 shrink-0">
                                    <span class="material-symbols-outlined text-sm">forum</span>
                                    <span class="text-xs">{{ $discussion['replies'] }}</span>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                <div class="flex -space-x-2">
                                    @foreach($discussion['avatars'] as $avatar)
                                        <div class="w-6 h-6 rounded-full border-2 border-white {{ $loop->first ? 'bg-sand text-slate-700' : 'bg-primary text-white' }} flex items-center justify-center text-[8px] font-bold">
                                            {{ $avatar }}
                                        </div>
                                    @endforeach
                                </div>

                                <span class="text-xs text-slate-400 italic">
                                    {{ $discussion['latest'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- UPSC Hub -->
            <section class="border-t border-sand pt-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold">UPSC Anthropology Hub</h2>
                        <p class="text-sm text-slate-500">Curated resources for Optional Paper I & II</p>
                    </div>

                    <button class="bg-primary/10 text-primary px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined">description</span>
                        Download Syllabus & Trend Analysis
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($upscResources as $resource)
                        <div class="bg-white p-6 rounded-xl border-l-4 {{ $resource['border'] }}">
                            <h5 class="font-bold text-slate-900 mb-2">{{ $resource['title'] }}</h5>
                            <p class="text-xs text-slate-500 mb-4">{{ $resource['description'] }}</p>
                            <a class="text-primary text-xs font-bold uppercase tracking-widest" href="#">{{ $resource['cta'] }}</a>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-3 space-y-8">
            <div class="bg-white p-6 rounded-xl border border-sand">
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">bolt</span>
                    Quick Actions
                </h3>

                <div class="space-y-3">
                    @foreach($quickActions as $action)
                        <button class="w-full text-left p-3 rounded-lg hover:bg-sand/30 flex items-center gap-3 transition-colors">
                            <span class="material-symbols-outlined text-primary">{{ $action['icon'] }}</span>
                            <span class="text-sm font-medium">{{ $action['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-sand">
                <h3 class="font-bold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Recent Activity
                </h3>

                <div class="space-y-6">
                    @foreach($recentActivities as $activity)
                        <div class="flex gap-4">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-sand flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-sm">{{ $activity['icon'] }}</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold">{{ $activity['text'] }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl bg-olive p-6 text-white group cursor-pointer">
                <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl opacity-10 group-hover:rotate-12 transition-transform">library_books</span>
                <h3 class="font-bold mb-2">Research Library</h3>
                <p class="text-xs opacity-80 mb-4">Access 50,000+ academic papers and rare ethnographic field notes.</p>
                <div class="flex items-center gap-2 text-xs font-bold">
                    Browse Collection
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </div>
            </div>
        </aside>
    </div>

    <!-- Membership Acquisition Modal -->
    <div 
        wire:key="membership-checkout-modal"
        x-data="{ show: @entangle('showCheckoutModal').live }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
    >
        <!-- Static Backdrop (Transparent element on top of blurred one to catch clicks if needed, but here we just blur and dim) -->
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(12px);"
            class="fixed inset-0"
        ></div>

        <!-- Modal Container -->
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300 transform"
            x-transition:enter-start="scale-95 opacity-0 translate-y-8"
            x-transition:enter-end="scale-100 opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-200 transform"
            x-transition:leave-start="scale-100 opacity-100 translate-y-0"
            x-transition:leave-end="scale-95 opacity-0 translate-y-8"
            class="bg-white rounded-3xl shadow-2xl ring-1 ring-black/5 w-full max-w-4xl max-h-[90vh] overflow-hidden relative z-10 flex flex-col md:flex-row"
        >
            @if($paymentSuccess)
                <!-- Success State -->
                <div class="flex-1 p-12 flex flex-col items-center justify-center text-center animate-in fade-in zoom-in duration-500">
                    <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mb-8">
                        <span class="material-symbols-outlined text-6xl text-primary" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    </div>
                    <h2 class="text-4xl font-headline italic font-bold text-on-surface mb-4">Welcome to the Inner Circle!</h2>
                    <p class="text-lg text-on-surface-variant max-w-md mb-8">
                        Your membership has been activated successfully. You now have full access to the AnthroConnect platform.
                    </p>
                    <div class="bg-sand/30 p-4 rounded-xl mb-10 text-xs font-mono text-primary border border-primary/10">
                        Transaction Ref: {{ $paymentReference }}
                    </div>
                    <button 
                        @click="show = false"
                        class="bg-primary text-white px-12 py-4 rounded-xl font-bold uppercase tracking-widest hover:opacity-90 transition-opacity"
                    >
                        Enter Dashboard
                    </button>
                </div>
            @else
                <!-- Left Side: Summary (Member Info) -->
                <div class="w-full md:w-5/12 bg-primary p-8 sm:p-12 text-white flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute -right-20 -bottom-20 opacity-10">
                        <span class="material-symbols-outlined text-[300px] rotate-12">workspace_premium</span>
                    </div>

                    <div class="relative z-10">
                        <div class="mb-10">
                            <h3 class="text-primary-container font-label text-[10px] uppercase font-bold tracking-[0.2em] mb-4">Checkout Summary</h3>
                            <h2 class="text-3xl font-headline italic font-bold leading-tight">{{ $globalSetting->title ?? 'AnthroConnect Membership' }}</h2>
                        </div>

                        <div class="space-y-6 mb-12">
                            <p class="text-sm opacity-80 leading-relaxed">{{ $globalSetting->description ?? 'Unlock the full potential of anthropological research.' }}</p>

                            @if($globalSetting)
                                <div class="space-y-3">
                                    @foreach($globalSetting->privileges as $privilege)
                                        <div class="flex items-center gap-3 text-xs">
                                            <span class="material-symbols-outlined text-primary-container text-sm">verified</span>
                                            {{ $privilege->privilege }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="relative z-10 pt-8 border-t border-white/10">
                        <div class="flex justify-between items-end">
                            <span class="text-xs uppercase tracking-widest opacity-60">Total to Pay</span>
                            <span class="text-4xl font-headline italic font-bold">₹ {{ number_format($globalSetting->price_inr ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Form (Dummy Payment) -->
                <div class="w-full md:w-7/12 p-8 sm:p-12 pb-16 flex flex-col bg-white">
                    <div class="flex justify-between items-center mb-10">
                        <h3 class="text-xl font-bold text-on-surface">Payment Information</h3>
                        <button 
                            @click="show = false" 
                            wire:loading.attr="disabled"
                            class="text-slate-400 hover:text-on-surface transition-colors"
                        >
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form wire:submit.prevent="processPurchase" class="flex-1 flex flex-col gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Cardholder Name</label>
                            <input 
                                wire:model.defer="cardName"
                                type="text" 
                                placeholder="Enter your full name"
                                class="w-full bg-sand/20 border-sand rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/50 @error('cardName') border-error @enderror"
                            />
                            @error('cardName') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Card Number</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">credit_card</span>
                                <input 
                                    wire:model.defer="cardNumber"
                                    type="text" 
                                    placeholder="0000 0000 0000 0000"
                                    class="w-full bg-sand/20 border-sand rounded-xl pl-12 pr-4 py-4 text-sm focus:ring-2 focus:ring-primary/50 @error('cardNumber') border-error @enderror"
                                />
                            </div>
                            @error('cardNumber') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Expiry Date</label>
                                <input 
                                    wire:model.defer="cardExpiry"
                                    type="text" 
                                    placeholder="MM / YY"
                                    class="w-full bg-sand/20 border-sand rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/50 @error('cardExpiry') border-error @enderror"
                                />
                                @error('cardExpiry') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">CVV</label>
                                <input 
                                    wire:model.defer="cardCvv"
                                    type="password" 
                                    placeholder="***"
                                    class="w-full bg-sand/20 border-sand rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/50 @error('cardCvv') border-error @enderror"
                                />
                                @error('cardCvv') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-0 mb-6 space-y-4">
                            @error('payment')
                                <div class="p-3 bg-error/10 text-error rounded-lg text-xs font-medium text-center">
                                    {{ $message }}
                                </div>
                            @enderror

                            <button 

                                type="submit"
                                wire:loading.attr="disabled"
                                class="w-full bg-primary text-white py-4 rounded-2xl font-bold uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:translate-y-0"
                            >
                                <span wire:loading.remove>Complete Membership</span>
                                <span wire:loading.flex class="items-center gap-2 whitespace-nowrap">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                            <p class="text-center text-[10px] text-white font-medium italic">
                                This is a simulated checkout. No real funds will be processed.
                            </p>
                            
                            
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
