<div>
    <!-- Page Title -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline text-4xl font-bold italic text-on-surface">Repository Overview</h2>
            <p class="text-on-surface-variant font-body mt-1">Managing the digital threads of human culture and evolution.</p>
        </div>
        <div class="flex space-x-3">
            @if (session()->has('message'))
                <div class="flex items-center text-[10px] font-bold text-primary bg-primary/10 px-4 py-2 rounded-lg animate-pulse">
                    {{ session('message') }}
                </div>
            @endif
            <button wire:click="generateReport('repository_overview')" wire:loading.attr="disabled" class="px-5 py-2.5 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg text-sm font-bold uppercase tracking-wider shadow-md hover:opacity-90 transition-opacity flex items-center">
                <span wire:loading.remove wire:target="generateReport('repository_overview')">Generate Global Report</span>
                <span wire:loading wire:target="generateReport('repository_overview')" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Generating...
                </span>
            </button>
        </div>
    </div>

    <!-- 1. Overview Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-primary/10">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Total Users</span>
            <div class="mt-4 flex items-baseline space-x-2">
                <span class="text-2xl font-bold font-sans">{{ number_format($stats['total_users']) }}</span>
                <span class="text-xs text-primary font-medium">Live</span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-primary/10">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Active Members</span>
            <div class="mt-4 flex items-baseline space-x-2">
                <span class="text-2xl font-bold font-sans">{{ number_format($stats['active_members']) }}</span>
                <span class="text-xs text-primary font-medium">
                    {{ $stats['total_users'] > 0 ? round(($stats['active_members'] / $stats['total_users']) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-primary/10">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Digital Archive</span>
            <div class="mt-4 flex items-baseline space-x-2">
                <span class="text-2xl font-bold font-sans">{{ number_format($stats['total_content']) }}</span>
                <span class="text-xs text-primary font-medium">Items</span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-primary/10">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Active Threads</span>
            <div class="mt-4 flex items-baseline space-x-2">
                <span class="text-2xl font-bold font-sans">{{ number_format($stats['active_threads']) }}</span>
                <span class="text-xs text-stone-400 font-medium">Community</span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-secondary/20">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Pending Evaluations</span>
            <div class="mt-4 flex items-baseline space-x-2">
                <span class="text-2xl font-bold font-sans text-error">{{ number_format($stats['pending_exams']) }}</span>
                <span class="text-xs text-tertiary font-medium italic">Action Required</span>
            </div>
        </div>
    </div>

    <!-- Bento Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 2. Content Management Overview (2 cols) -->
        <div class="lg:col-span-2 space-y-8">
            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="font-headline text-2xl italic text-on-surface">Archival Logs</h3>
                    <a href="{{ route('admin.explore.index') }}" class="text-xs text-primary font-bold uppercase hover:underline">View Repository</a>
                </div>
                <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/20">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-stone-50 border-b border-stone-100">
                            <tr>
                                <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold">Entry</th>
                                <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold">Discipline</th>
                                <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold">Captured</th>
                                <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($recentActivities as $activity)
                            <tr class="hover:bg-stone-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-sm text-stone-900">{{ $activity['title'] }}</p>
                                    <p class="text-[10px] text-stone-400 italic">{{ $activity['meta'] }}</p>
                                </td>
                                <td class="px-6 py-4 text-xs text-stone-600">{{ $activity['category'] }}</td>
                                <td class="px-6 py-4 text-xs text-stone-500">{{ $activity['time'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-primary/10 text-primary rounded-full text-[10px] font-bold">{{ $activity['status'] }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-stone-400 italic text-sm">No recent activity detected in the archives.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Charts Container -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <!-- 4. User Growth Chart -->
                <div class="bg-surface-container-lowest rounded-xl shadow-sm p-6 border border-outline-variant/10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="space-y-1">
                            <h3 class="font-headline text-xl italic text-on-surface">Registration Trends</h3>
                            <p class="text-[10px] text-stone-400 uppercase tracking-widest">Monthly Archivist Intake ({{ date('Y') }})</p>
                        </div>
                        <button wire:click="generateReport('registration_trends')" class="text-stone-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">downloading</span>
                        </button>
                    </div>
                    
                    @php
                        $maxChart = max($chartData) > 0 ? max($chartData) : 10;
                    @endphp

                    <div class="h-48 w-full flex items-end justify-between space-x-2 relative pt-6">
                        <div class="absolute inset-x-0 top-6 border-t border-stone-100"></div>
                        <div class="absolute inset-x-0 top-1/2 border-t border-stone-100"></div>
                        <div class="absolute inset-x-0 bottom-0 border-t border-stone-200"></div>
                        
                        @foreach($chartData as $month => $count)
                            <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                                <div class="absolute bottom-full mb-1 bg-stone-800 text-white text-[10px] px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10 whitespace-nowrap">
                                    {{ $count }}
                                </div>
                                <div class="w-full bg-primary/20 rounded-t-sm transition-all duration-500 group-hover:bg-primary/40"
                                     style="height: {{ ($count / $maxChart) * 100 }}%">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between mt-3 text-[10px] font-bold text-stone-400 uppercase tracking-tighter px-1">
                        <span>J</span><span>F</span><span>M</span><span>A</span><span>M</span><span>J</span><span>J</span><span>A</span><span>S</span><span>O</span><span>N</span><span>D</span>
                    </div>
                </div>

                <!-- 5. Subscription Trends Chart -->
                <div class="bg-surface-container-lowest rounded-xl shadow-sm p-6 border border-outline-variant/10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="space-y-1">
                            <h3 class="font-headline text-xl italic text-on-surface">Subscription Growth</h3>
                            <p class="text-[10px] text-stone-400 uppercase tracking-widest">Monthly Membership Acquisition ({{ date('Y') }})</p>
                        </div>
                        <button wire:click="generateReport('subscription_trends')" class="text-stone-400 hover:text-secondary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">downloading</span>
                        </button>
                    </div>
                    
                    @php
                        $maxSub = max($subscriptionData) > 0 ? max($subscriptionData) : 10;
                    @endphp

                    <div class="h-48 w-full flex items-end justify-between space-x-2 relative pt-6">
                        <div class="absolute inset-x-0 top-6 border-t border-stone-100"></div>
                        <div class="absolute inset-x-0 top-1/2 border-t border-stone-100"></div>
                        <div class="absolute inset-x-0 bottom-0 border-t border-stone-200"></div>
                        
                        @foreach($subscriptionData as $month => $count)
                            <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                                <div class="absolute bottom-full mb-1 bg-stone-800 text-white text-[10px] px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10 whitespace-nowrap">
                                    {{ $count }}
                                </div>
                                <div class="w-full bg-secondary/20 rounded-t-sm transition-all duration-500 group-hover:bg-secondary/40"
                                     style="height: {{ ($count / $maxSub) * 100 }}%">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between mt-3 text-[10px] font-bold text-stone-400 uppercase tracking-tighter px-1">
                        <span>J</span><span>F</span><span>M</span><span>A</span><span>M</span><span>J</span><span>J</span><span>A</span><span>S</span><span>O</span><span>N</span><span>D</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Grid (1 col) -->
        <div class="space-y-8">
            <!-- 5. Command Center (Quick Actions) -->
            <div class="bg-stone-50/80 rounded-2xl p-6 border border-stone-200/50">
                <h3 class="font-headline text-xl italic text-on-surface mb-6">Command Center</h3>
                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('admin.lms.modules.create') }}" class="flex items-center justify-between p-4 bg-white rounded-xl border border-stone-100 hover:border-primary hover:shadow-lg hover:shadow-primary/5 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mr-4 group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[20px]">school</span>
                            </div>
                            <span class="text-sm font-bold text-stone-700">Add New Module</span>
                        </div>
                        <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors text-[20px]">arrow_forward</span>
                    </a>
                    <a href="{{ route('admin.explore.index') }}" class="flex items-center justify-between p-4 bg-white rounded-xl border border-stone-100 hover:border-primary hover:shadow-lg hover:shadow-primary/5 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mr-4 group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[20px]">article</span>
                            </div>
                            <span class="text-sm font-bold text-stone-700">Write Exploration</span>
                        </div>
                        <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors text-[20px]">arrow_forward</span>
                    </a>
                    <a href="{{ route('admin.community.discussions.index') }}" class="flex items-center justify-between p-4 bg-white rounded-xl border border-stone-100 hover:border-primary hover:shadow-lg hover:shadow-primary/5 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mr-4 group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[20px]">forum</span>
                            </div>
                            <span class="text-sm font-bold text-stone-700">Moderate Threads</span>
                        </div>
                        <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors text-[20px]">arrow_forward</span>
                    </a>
                    <a href="{{ route('admin.library.resources.index') }}" class="flex items-center justify-between p-4 bg-white rounded-xl border border-stone-100 hover:border-primary hover:shadow-lg hover:shadow-primary/5 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mr-4 group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[20px]">cloud_upload</span>
                            </div>
                            <span class="text-sm font-bold text-stone-700">Import Research</span>
                        </div>
                        <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors text-[20px]">arrow_forward</span>
                    </a>
                </div>
            </div>

            <!-- 3. Community & Moderation Activity -->
            <div class="space-y-6">
                <div class="flex items-center justify-between px-2">
                    <h3 class="font-headline text-xl italic text-on-surface">Action Queue</h3>
                    @if($stats['pending_exams'] > 0)
                        <span class="bg-error text-white text-[10px] px-2 py-0.5 rounded-full font-bold animate-pulse">{{ $stats['pending_exams'] }}</span>
                    @endif
                </div>
                <div class="space-y-4">
                    @if($stats['pending_exams'] > 0)
                    <div class="bg-white p-5 rounded-2xl border border-stone-100 shadow-sm relative overflow-hidden group hover:border-error/20 transition-all">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-error"></div>
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-error uppercase tracking-widest">UPSC Evaluation</span>
                                <span class="text-[11px] text-stone-500 font-medium">Pending Submissions</span>
                            </div>
                            <span class="text-[10px] text-stone-400 font-bold bg-stone-50 px-2 py-1 rounded">PRIORITY</span>
                        </div>
                        <p class="text-sm text-stone-700 font-medium mb-4 italic">The evaluation queue is currently at <span class="text-error font-bold">{{ $stats['pending_exams'] }}</span>. New responses require archivist feedback.</p>
                        <a href="{{ route('admin.exams.submissions.index') }}" class="w-full py-2.5 bg-stone-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-lg flex items-center justify-center hover:bg-stone-800 transition-colors">
                            Access Queue
                        </a>
                    </div>
                    @else
                    <div class="bg-stone-50 border border-dashed border-stone-200 p-8 rounded-2xl text-center">
                        <span class="material-symbols-outlined text-stone-300 text-4xl mb-2">check_circle</span>
                        <p class="text-xs text-stone-500 italic">No pending actions in the queue. The repository is up to date.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Spacer -->
    <div class="h-20"></div>
</div>
