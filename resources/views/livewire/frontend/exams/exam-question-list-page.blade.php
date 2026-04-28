<div class="min-h-screen bg-stone-50 py-12">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Hero Section -->
        <div class="mb-16">
            <h1 class="text-4xl lg:text-6xl font-headline text-stone-900 mb-4 italic">Practice <span class="text-orange-800">Exams</span></h1>
            <p class="text-lg text-stone-600 max-w-2xl font-body">Master the art of answer writing with our curated database of UPSC anthropology questions. Track your progress, timing, and word count.</p>
        </div>

        @if($qotd)
            <!-- Question of the Day Spotlight -->
            <div class="mb-16 relative overflow-hidden bg-stone-900 rounded-[3rem] p-8 lg:p-12 text-white shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-800/20 blur-[100px] rounded-full"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="px-4 py-1 rounded-full bg-orange-800 text-[10px] font-bold uppercase tracking-widest">Spotlight</span>
                        <span class="text-xs font-bold text-stone-400 uppercase tracking-widest">Question of the Day</span>
                    </div>
                    <div class="grid lg:grid-cols-3 gap-12 items-end">
                        <div class="lg:col-span-2">
                            <h2 class="text-2xl lg:text-4xl font-headline italic mb-6 leading-tight">
                                {{ Str::limit(strip_tags($qotd->question_text), 180) }}
                            </h2>
                            <div class="flex flex-wrap gap-6 text-xs text-stone-400 font-bold uppercase tracking-widest">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">calendar_month</span>
                                    {{ $qotd->year ?: 'General' }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">payments</span>
                                    {{ $qotd->marks }} Marks
                                </div>
                                @if($qotd->is_members_only)
                                    <div class="flex items-center gap-2 text-orange-200">
                                        <span class="material-symbols-outlined text-sm">lock</span>
                                        Members Only
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <a wire:navigate href="{{ route('exams.show', $qotd->slug) }}" class="group bg-white text-stone-900 px-10 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-orange-50 transition-all flex items-center gap-2 shadow-xl">
                                Start Practice
                                <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-stone-200 mb-12">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-[280px] relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search by keywords or themes..." class="w-full bg-stone-50 border-none rounded-2xl pl-12 pr-4 py-4 text-sm focus:ring-2 focus:ring-orange-800 transition-all">
                </div>

                <div class="flex flex-wrap gap-3">
                    <select wire:model.live="year" class="bg-stone-50 border-none rounded-2xl px-6 py-4 text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-orange-800 cursor-pointer">
                        <option value="">All Years</option>
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>

                    <!-- Tags Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button" class="bg-stone-50 border-none rounded-2xl px-6 py-4 text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-orange-800 cursor-pointer flex items-center gap-2">
                            <span>Filter by Tags</span>
                            @if(count($selectedTags))
                                <span class="bg-orange-800 text-white w-4 h-4 rounded-full flex items-center justify-center text-[8px]">{{ count($selectedTags) }}</span>
                            @endif
                            <span class="material-symbols-outlined text-sm transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-72 max-h-96 overflow-y-auto bg-white rounded-3xl shadow-2xl border border-stone-100 z-50 p-6">
                            @forelse($tagGroups as $group)
                                <div class="mb-6 last:mb-0">
                                    <p class="text-[8px] uppercase font-bold text-stone-400 tracking-widest mb-3">{{ $group->name }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($group->activeTags as $tag)
                                            <label class="cursor-pointer group">
                                                <input type="checkbox" wire:model.live="selectedTags" value="{{ $tag->id }}" class="sr-only peer">
                                                <div class="px-3 py-1.5 rounded-xl border border-stone-100 bg-stone-50 text-[9px] font-bold text-stone-500 peer-checked:bg-orange-800 peer-checked:text-white peer-checked:border-orange-800 transition-all">
                                                    {{ $tag->name }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-[10px] text-stone-400 italic">No tags available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions List -->
        <div class="flex flex-col gap-6">
            @forelse($questions as $question)
                <div class="bg-white rounded-[2rem] border border-stone-200 overflow-hidden flex group hover:shadow-2xl hover:shadow-stone-200/30 hover:border-orange-800/20 transition-all duration-500 min-h-[140px]">
                    <div class="p-6 lg:p-8 flex-1 flex items-center gap-4 lg:gap-10">
                        <!-- Date/Marks Badge -->
                        <div class="flex flex-col gap-2 shrink-0 text-center w-16 lg:w-24">
                            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">{{ $question->year ?: 'General' }}</span>
                            <div class="px-2 lg:px-4 py-2 lg:py-3 bg-stone-50 rounded-2xl border border-stone-100">
                                <span class="text-xl lg:text-2xl font-black text-stone-900 font-headline leading-none">{{ $question->marks }}</span>
                                <span class="text-[8px] font-bold text-stone-400 block uppercase tracking-tighter mt-1">Marks</span>
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-3">
                                @if($question->is_members_only)
                                    <span class="material-symbols-outlined text-xs text-orange-800" title="Members Only">lock</span>
                                @endif
                                <span class="text-[8px] font-bold text-stone-400 uppercase tracking-[0.2em]">{{ $question->exam_type ?: 'UPSC' }} Anthropology</span>
                            </div>

                            <h3 class="text-lg lg:text-xl font-headline font-bold text-stone-900 italic leading-snug line-clamp-2 lg:line-clamp-1">
                                {{ strip_tags($question->question_text) }}
                            </h3>
                            
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach($question->tags->take(5) as $tag)
                                    <span class="px-3 py-1 rounded-full bg-stone-50 text-[9px] font-bold text-stone-400 uppercase tracking-widest border border-stone-100 group-hover:border-stone-200 transition-colors">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <a wire:navigate href="{{ route('exams.show', $question->slug) }}" class="w-16 lg:w-56 bg-stone-50 border-l border-stone-100 group-hover:bg-orange-800 transition-all duration-500 flex flex-col justify-center items-center gap-3">
                        <span class="hidden lg:block text-[10px] font-bold uppercase tracking-widest text-stone-900 group-hover:text-white transition-colors">Start Practice</span>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full border border-stone-200 flex items-center justify-center group-hover:border-white/30 group-hover:bg-white/10 transition-all">
                            <span class="material-symbols-outlined text-xl lg:text-2xl text-stone-400 group-hover:text-white transition-transform group-hover:translate-x-1">arrow_forward</span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="py-32 text-center">
                    <div class="flex flex-col items-center opacity-30">
                        <span class="material-symbols-outlined text-6xl mb-4 text-stone-300">edit_note</span>
                        <p class="font-headline text-2xl italic text-stone-400">No questions found matching your filters.</p>
                        <p class="text-xs uppercase tracking-widest mt-2 text-stone-400">Try broadening your search criteria.</p>
                    </div>
                </div>
            @endforelse
        </div>v>

        <!-- Pagination -->
        <div class="mt-16">
            {{ $questions->links() }}
        </div>
    </div>
</div>
