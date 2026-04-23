<div class="pb-24 pt-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 overflow-x-auto whitespace-nowrap scrollbar-hide py-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('community.index') }}" class="text-[10px] font-bold text-stone-400 uppercase tracking-widest hover:text-primary transition-colors">Community</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-stone-300 text-sm mx-1">chevron_right</span>
                        <a href="{{ route('community.index', ['topic' => $discussion->topic?->slug]) }}" class="text-[10px] font-bold text-stone-400 uppercase tracking-widest hover:text-primary transition-colors">{{ $discussion->topic?->name }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-stone-300 text-sm mx-1">chevron_right</span>
                        <span class="text-[10px] font-bold text-stone-900 uppercase tracking-widest truncate max-w-[200px]">{{ $discussion->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Main Content Area -->
            <div class="lg:col-span-8 space-y-12">
                
                <!-- Main Discussion Card -->
                <article class="bg-white rounded-[40px] border border-stone-200 shadow-sm overflow-hidden">
                    <div class="p-8 sm:p-12 space-y-8">
                        <!-- Discussion Header -->
                        <div class="space-y-6">
                            <h1 class="text-3xl md:text-5xl font-headline font-bold text-stone-900 italic leading-tight">
                                {{ $discussion->title }}
                            </h1>
                            
                            <div class="flex flex-wrap items-center justify-between gap-6 pb-8 border-b border-stone-100">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-primary/10">
                                        <img src="{{ $discussion->author?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($discussion->author?->name ?? 'Scholar') }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-stone-900 leading-none">{{ $discussion->author?->name }}</p>
                                        <p class="text-[10px] text-stone-500 uppercase tracking-widest mt-1">Independent Scholar • {{ $discussion->published_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button wire:click="vote('discussion', {{ $discussion->id }}, 1)" class="flex items-center gap-2 px-4 py-2 rounded-xl border {{ $discussion->votes()->where('user_id', Auth::id())->where('vote', 1)->exists() ? 'bg-primary text-white border-primary' : 'bg-stone-50 text-stone-600 border-stone-100 hover:border-primary' }} transition-all">
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">thumb_up</span>
                                        <span class="text-xs font-bold">{{ number_format($discussion->likes_count) }}</span>
                                    </button>
                                    <div class="h-8 w-px bg-stone-100 mx-2"></div>
                                    <button wire:click="$toggle('showTopComposer')" class="flex items-center gap-2 px-5 py-2.5 bg-stone-900 text-white rounded-xl hover:bg-stone-800 transition-all shadow-lg shadow-stone-200">
                                        <span class="material-symbols-outlined text-sm font-bold">add</span>
                                        <span class="text-xs font-bold uppercase tracking-widest">Contribute</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Top Level Composer (revealed via button) -->
                        <div x-data="{ open: @entangle('showTopComposer') }" x-show="open" x-transition.origin.top class="mt-8 pb-8 border-b border-stone-100">
                            @include('livewire.public.community._discussion-composer', ['replyingTo' => null, 'placeholder' => 'Add your high-level perspective to this inquiry...'])
                        </div>

                        <!-- Content Body -->
                        <div class="prose prose-stone max-w-none">
                            <div class="text-lg text-stone-800 leading-relaxed font-body">
                                {!! nl2br(e($discussion->body)) !!}
                            </div>
                        </div>

                        <!-- Discussion Metadata -->
                        <div class="pt-8 flex flex-wrap items-center gap-4">
                            @foreach($discussion->tags as $tag)
                                <a href="{{ route('community.index', ['tag' => $tag->slug]) }}" class="px-4 py-2 bg-stone-50 text-stone-600 text-[10px] font-bold uppercase tracking-widest rounded-xl border border-stone-100 hover:border-primary transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </article>

                <!-- Expert Insights Section -->
                @if($expertInsights->isNotEmpty())
                    <section class="space-y-6">
                        <div class="flex items-center gap-4 px-4">
                            <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                            <h2 class="text-xs font-bold text-stone-900 uppercase tracking-widest">Expert Insights</h2>
                        </div>
                        
                        <div class="space-y-6">
                            @foreach($expertInsights as $insight)
                                <div class="relative bg-stone-900 rounded-[32px] p-8 border border-stone-800 shadow-xl overflow-hidden group">
                                    <div class="absolute inset-0 opacity-5 ethno-pattern"></div>
                                    <div class="relative z-10 space-y-6">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full border border-primary/30 overflow-hidden">
                                                    <img src="{{ $insight->author?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($insight->author?->name ?? 'Expert') }}" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-white leading-none">{{ $insight->author?->name }}</p>
                                                    <p class="text-[9px] text-primary uppercase tracking-widest mt-1">Verified Researcher • Expert</p>
                                                </div>
                                            </div>
                                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                                        </div>
                                        <div class="text-stone-300 text-sm leading-relaxed italic border-l-2 border-primary/20 pl-6">
                                            "{!! nl2br(e($insight->body)) !!}"
                                        </div>
                                        <div class="flex items-center justify-between pt-4 border-t border-white/5">
                                            <span class="text-[9px] text-stone-500 uppercase font-bold tracking-widest">{{ $insight->published_at->diffForHumans() }}</span>
                                            <button wire:click="setReplyingTo({{ $insight->id }})" class="text-[10px] font-bold text-primary hover:text-white uppercase tracking-widest transition-colors">Engage Insight</button>
                                        </div>

                                        <!-- Inline Reply Composer for Expert Insight -->
                                        @if($replyingTo == $insight->id)
                                            <div class="mt-6 pt-6 border-t border-white/5" x-data x-transition.origin.top>
                                                @include('livewire.public.community._discussion-composer', [
                                                    'replyingTo' => $insight->id, 
                                                    'compact' => true, 
                                                    'placeholder' => 'Add your peer review to this verified insight...'
                                                ])
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Community Dialogue (Replies) -->
                <section class="space-y-8 pt-6">
                    <div class="flex items-center justify-between px-4">
                        <div class="flex items-center gap-4">
                            <span class="w-1.5 h-6 bg-stone-200 rounded-full"></span>
                            <h2 class="text-xs font-bold text-stone-900 uppercase tracking-widest">Community Dialogue ({{ $discussion->replies_count }})</h2>
                        </div>
                        <div class="flex items-center gap-2">
                             <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Sort:</span>
                             <select class="bg-transparent border-none text-[10px] font-bold text-stone-900 uppercase tracking-widest focus:ring-0 cursor-pointer">
                                 <option>Scholarly Relevancy</option>
                                 <option>Chronological</option>
                             </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($replies as $reply)
                            @include('livewire.public.community._reply-item', ['reply' => $reply])
                        @empty
                            <div class="py-20 text-center bg-stone-50 rounded-[40px] border-2 border-dashed border-stone-200">
                                <span class="material-symbols-outlined text-4xl text-stone-200 mb-4">forum</span>
                                <p class="text-stone-400 text-sm font-medium">No scholarship contributions yet. Be the first to engage.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

            </div>

            <!-- Right Sidebar Column -->
            <div class="lg:col-span-4 space-y-12">
                <!-- Discussion Metadata Widget -->
                <div class="bg-white rounded-[40px] p-8 border border-stone-200 shadow-sm space-y-8">
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-stone-900 uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-sm">analytics</span>
                            Inquiry Metrics
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-stone-50 border border-stone-100">
                                <p class="text-xl font-bold text-stone-900">{{ number_format($discussion->likes_count) }}</p>
                                <p class="text-[9px] font-bold text-stone-400 uppercase tracking-tighter">Scholarly Likes</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-stone-50 border border-stone-100">
                                <p class="text-xl font-bold text-stone-900">{{ $discussion->replies_count }}</p>
                                <p class="text-[9px] font-bold text-stone-400 uppercase tracking-tighter">Contributions</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-stone-100">
                        <h3 class="text-xs font-bold text-stone-900 uppercase tracking-widest mb-6">Key Concepts</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($discussion->tags as $tag)
                                <span class="px-4 py-2 bg-stone-100 rounded-xl text-[10px] font-bold text-stone-600">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Related Discussions Widget -->
                <section class="space-y-6">
                    <h3 class="text-xs font-bold text-stone-900 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        Related Inquiries
                    </h3>
                    <div class="space-y-6">
                        @foreach($relatedDiscussions as $rd)
                            <a href="{{ route('community.show', $rd->slug) }}" class="flex gap-4 group cursor-pointer">
                                <div class="shrink-0 w-12 h-12 rounded-2xl bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400 group-hover:bg-primary group-hover:text-white transition-all overflow-hidden text-sm uppercase font-bold">
                                    {{ substr($rd->topic?->name, 0, 2) }}
                                </div>
                                <div class="space-y-1">
                                    <h5 class="text-sm font-bold text-stone-800 leading-tight group-hover:text-primary transition-colors line-clamp-2">{{ $rd->title }}</h5>
                                    <p class="text-[9px] text-stone-400 font-bold uppercase tracking-widest">{{ $rd->replies_count }} Replies • {{ $rd->topic?->name }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>

                <!-- Guidelines Widget -->
                <section class="bg-stone-900 rounded-[40px] p-8 border border-stone-800 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                    <div class="relative z-10 space-y-6">
                        <h3 class="text-xs font-bold text-primary uppercase tracking-widest">Scholar Guidelines</h3>
                        <ul class="space-y-4 text-xs text-stone-400 font-medium">
                            <li class="flex gap-3">
                                <span class="material-symbols-outlined text-stone-600 text-sm">check_circle</span>
                                Maintain academic rigor and evidence-based analysis.
                            </li>
                            <li class="flex gap-3">
                                <span class="material-symbols-outlined text-stone-600 text-sm">check_circle</span>
                                Cite relevant ethnographic data where possible.
                            </li>
                            <li class="flex gap-3">
                                <span class="material-symbols-outlined text-stone-600 text-sm">check_circle</span>
                                Respect diverse ontological perspectives.
                            </li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

</div>
