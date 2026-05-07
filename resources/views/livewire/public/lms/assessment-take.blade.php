<div class="min-h-screen bg-stone-50" x-data="{ showConfirmModal: false }">
    @if($state === 'intro')
        <!-- Intro Screen -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-24">
            <div class="bg-white rounded-[2.5rem] shadow-2xl border border-stone-200 overflow-hidden animate-in fade-in zoom-in-95 duration-700">
                <div class="bg-stone-900 p-8 md:p-16 text-white relative">
                    <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 rounded-2xl bg-primary/20 text-primary flex items-center justify-center mb-8 shadow-inner">
                            <span class="material-symbols-outlined text-3xl">psychology</span>
                        </div>
                        <h1 class="font-headline text-3xl md:text-6xl font-bold italic mb-4 leading-tight tracking-tight">{{ $assessment->title }}</h1>
                        <p class="text-stone-400 text-sm md:text-xl font-light italic leading-relaxed max-w-2xl">{{ $assessment->description ?: 'Scholarly challenge for unit mastery.' }}</p>
                    </div>
                </div>

                <div class="p-8 md:p-16 space-y-12">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
                        <div class="p-8 rounded-3xl bg-stone-50 border border-stone-100 flex flex-col items-center text-center group hover:bg-white hover:border-primary/20 transition-all duration-300">
                            <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-stone-400 text-2xl group-hover:text-primary transition-colors">timer</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400 mb-2">Duration</span>
                            <span class="text-xl font-headline font-bold text-stone-900 italic">{{ $assessment->duration_minutes ?: 'Unlimited' }} Minutes</span>
                        </div>
                        <div class="p-8 rounded-3xl bg-stone-50 border border-stone-100 flex flex-col items-center text-center group hover:bg-white hover:border-primary/20 transition-all duration-300">
                            <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-stone-400 text-2xl group-hover:text-primary transition-colors">quiz</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400 mb-2">Structure</span>
                            <span class="text-xl font-headline font-bold text-stone-900 italic">{{ $assessment->questions->count() }} Questions</span>
                        </div>
                        <div class="p-8 rounded-3xl bg-stone-50 border border-stone-100 flex flex-col items-center text-center group hover:bg-white hover:border-primary/20 transition-all duration-300 sm:col-span-2 md:col-span-1">
                            <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-stone-400 text-2xl group-hover:text-primary transition-colors">grade</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400 mb-2">Mastery Goal</span>
                            <span class="text-xl font-headline font-bold text-stone-900 italic">{{ $assessment->passing_marks ?: 0 }}% Mastery</span>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary whitespace-nowrap">Scholarly Protocols</span>
                            <div class="h-px bg-stone-100 flex-1"></div>
                        </div>
                        <div class="prose prose-stone max-w-none text-stone-600 italic leading-relaxed text-sm md:text-base">
                            {!! nl2br(e($assessment->instructions ?: 'Please answer all questions to the best of your ability. Your progress is saved automatically.')) !!}
                        </div>
                    </div>

                    <div class="pt-8 border-t border-stone-100 flex justify-center">
                        <button wire:click="startTaking" class="w-full sm:w-auto bg-stone-900 text-white px-12 py-5 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-2xl shadow-stone-900/20 hover:-translate-y-1 hover:bg-primary transition-all flex items-center justify-center gap-4 group">
                            Begin Final Submission
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($state === 'taking')
        <!-- Active Test UI -->
        <div class="flex flex-col h-[calc(100vh-64px)] overflow-hidden">
            <!-- Test Header -->
            <header class="bg-white border-b border-stone-200 shrink-0 z-20 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 md:gap-6 min-w-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-stone-100 flex items-center justify-center text-stone-500 shrink-0">
                            <span class="material-symbols-outlined text-sm md:text-base">psychology</span>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-xs md:text-sm font-bold text-stone-900 italic truncate max-w-[120px] sm:max-w-xs">{{ $assessment->title }}</h2>
                            <p class="text-[7px] md:text-[8px] font-bold uppercase tracking-widest text-stone-400">Item {{ $currentQuestionIndex + 1 }} of {{ $assessment->questions->count() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-8">
                        @if($timeLeft !== -1)
                            <div class="flex items-center gap-2 md:gap-4 bg-stone-900 text-white px-3 py-2 md:px-6 md:py-3 rounded-xl md:rounded-2xl shadow-xl" 
                                 x-data="{ 
                                     time: @entangle('timeLeft'),
                                     isSubmitting: false,
                                     format(s) {
                                         const totalSeconds = Math.max(0, Math.floor(s));
                                         const m = Math.floor(totalSeconds / 60);
                                         const sec = totalSeconds % 60;
                                         return `${m}:${sec.toString().padStart(2, '0')}`;
                                     },
                                     tick() {
                                         if (this.time > 0) {
                                             this.time--;
                                             if (this.time <= 0 && !this.isSubmitting) {
                                                 this.isSubmitting = true;
                                                 $wire.submit();
                                             }
                                         }
                                     }
                                 }"
                                 x-init="setInterval(() => tick(), 1000)">
                                <span class="material-symbols-outlined text-xs md:text-base text-orange-500" :class="time < 60 ? 'animate-pulse' : ''">timer</span>
                                <span class="text-sm md:text-xl font-mono font-black tracking-tighter text-orange-500" x-text="format(time)"></span>
                            </div>
                        @endif

                        <button @click="showConfirmModal = true" class="bg-primary text-white px-4 py-2 md:px-8 md:py-2.5 rounded-lg md:rounded-xl font-bold uppercase tracking-widest text-[8px] md:text-[9px] shadow-lg shadow-primary/20 hover:scale-105 transition-all whitespace-nowrap">
                            Submit
                        </button>
                    </div>
                </div>
            </header>

            <div class="flex-1 flex overflow-hidden max-w-7xl mx-auto w-full px-0 sm:px-6 lg:px-8">
                <!-- Navigation Sidebar (Hidden on mobile) -->
                <aside class="w-80 bg-white border-r border-stone-100 p-8 flex flex-col gap-8 shrink-0 overflow-y-auto scrollbar-hide hidden lg:flex">
                    <div class="space-y-4">
                        <h4 class="text-[9px] font-bold uppercase tracking-widest text-stone-400">Navigation Matrix</h4>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($assessment->questions as $idx => $q)
                                <button wire:click="goToQuestion({{ $idx }})" 
                                        class="aspect-square rounded-lg flex items-center justify-center text-[10px] font-bold transition-all border
                                        {{ $currentQuestionIndex === $idx ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : (isset($answers[$q->id]) ? 'bg-stone-900 text-white border-stone-900' : 'bg-stone-50 text-stone-400 border-stone-100 hover:border-stone-300') }}">
                                    {{ $idx + 1 }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-auto p-6 rounded-2xl bg-stone-50 border border-stone-100">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="material-symbols-outlined text-primary text-sm">cloud_done</span>
                            <span class="text-[9px] font-bold uppercase tracking-widest text-stone-500">Auto-Saving</span>
                        </div>
                        <p class="text-[10px] text-stone-400 italic leading-relaxed">Your answers are synchronized with the archives in real-time.</p>
                    </div>
                </aside>

                <!-- Question Area -->
                <main class="flex-1 overflow-y-auto p-6 md:p-12 flex flex-col items-center scrollbar-hide">
                    <div class="w-full max-w-3xl space-y-8 md:space-y-12">
                        @php $currentQuestion = $assessment->questions[$currentQuestionIndex]; @endphp
                        
                        <div class="space-y-6 md:space-y-8">
                            <div class="flex items-start gap-4 md:gap-6">
                                <span class="w-8 h-8 md:w-12 md:h-12 rounded-lg md:rounded-2xl bg-stone-900 text-white flex items-center justify-center font-headline text-sm md:text-xl font-bold italic shrink-0 shadow-lg">
                                    {{ $currentQuestionIndex + 1 }}
                                </span>
                                <h3 class="font-headline text-lg md:text-3xl font-bold italic text-stone-900 leading-snug pt-1">
                                    {{ $currentQuestion->question_text }}
                                </h3>
                            </div>

                            <div class="grid gap-3 sm:pl-16 md:pl-20">
                                @foreach($currentQuestion->options as $option)
                                    <button wire:click="selectOption({{ $currentQuestion->id }}, {{ $option->id }})" 
                                            class="w-full text-left p-4 md:p-5 rounded-2xl border transition-all flex items-center gap-4 group
                                            {{ (isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] == $option->id) ? 'bg-stone-900 border-stone-900 text-white shadow-xl translate-x-1' : 'bg-white border-stone-100 text-stone-600 hover:border-primary/30 hover:bg-stone-50' }}">
                                        <div class="w-5 h-5 md:w-6 md:h-6 rounded-full border-2 flex items-center justify-center shrink-0 
                                            {{ (isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] == $option->id) ? 'border-primary bg-primary text-white' : 'border-stone-200 group-hover:border-primary' }}">
                                            @if(isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] == $option->id)
                                                <span class="material-symbols-outlined text-[10px] md:text-[14px] font-bold">check</span>
                                            @endif
                                        </div>
                                        <span class="text-sm md:text-lg font-medium italic">{{ $option->option_text }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer Navigation -->
                        <div class="pt-6 border-t border-stone-100 flex items-center justify-between gap-4">
                            <button wire:click="prevQuestion" 
                                    @if($currentQuestionIndex === 0) disabled @endif
                                    class="flex-1 sm:flex-none flex items-center justify-center gap-3 text-[10px] font-bold uppercase tracking-widest text-stone-500 bg-white border border-stone-200 px-4 sm:px-8 py-3.5 rounded-xl shadow-sm hover:border-primary hover:text-primary disabled:opacity-0 transition-all group">
                                <span class="material-symbols-outlined text-base group-hover:-translate-x-1 transition-transform">arrow_back</span>
                                <span class="hidden sm:inline">Previous</span>
                            </button>

                            <div class="flex items-center gap-2 hidden sm:flex">
                                @foreach($assessment->questions as $idx => $q)
                                    <div class="w-1.5 h-1.5 rounded-full transition-all duration-300 {{ $currentQuestionIndex === $idx ? 'w-6 bg-primary' : (isset($answers[$q->id]) ? 'bg-stone-900' : 'bg-stone-200') }}"></div>
                                @endforeach
                            </div>

                            @if($currentQuestionIndex < $assessment->questions->count() - 1)
                                <button wire:click="nextQuestion" class="flex-1 sm:flex-none flex items-center justify-center gap-3 text-[10px] font-bold uppercase tracking-widest text-white bg-stone-900 px-4 sm:px-8 py-3.5 rounded-xl shadow-xl hover:bg-primary transition-all group">
                                    <span class="hidden sm:inline">Next Question</span>
                                    <span class="sm:hidden">Next</span>
                                    <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </button>
                            @else
                                <button @click="showConfirmModal = true" class="flex-1 sm:flex-none flex items-center justify-center gap-3 text-[10px] font-bold uppercase tracking-widest text-white bg-emerald-600 px-4 sm:px-8 py-3.5 rounded-xl shadow-xl shadow-emerald-600/20 hover:scale-105 transition-all">
                                    Complete
                                    <span class="material-symbols-outlined text-base">done_all</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Submission Confirmation Modal -->
        <div x-show="showConfirmModal" 
             class="fixed inset-0 z-[150] flex items-center justify-center p-4 sm:p-6" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-md" @click="showConfirmModal = false"></div>
            
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg relative z-10 overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="bg-stone-900 p-8 md:p-12 text-white text-center relative">
                    <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-3xl bg-primary/20 text-primary flex items-center justify-center mx-auto mb-6 shadow-inner">
                            <span class="material-symbols-outlined text-4xl">inventory_2</span>
                        </div>
                        <h3 class="font-headline text-2xl md:text-3xl font-bold italic mb-2">Finalize Assessment?</h3>
                        <p class="text-stone-400 text-xs md:text-sm italic px-4">You are about to commit your responses to the permanent archives. This action is irreversible.</p>
                    </div>
                </div>
                
                <div class="p-8 md:p-12 space-y-8">
                    <div class="flex items-center justify-between p-6 rounded-2xl bg-stone-50 border border-stone-100">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Answered</span>
                            <span class="text-xl font-bold text-stone-900">{{ count($answers) }} / {{ $assessment->questions->count() }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Status</span>
                            <span class="text-[10px] font-bold text-primary italic uppercase tracking-widest">Ready</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button @click="showConfirmModal = false" class="order-2 sm:order-1 px-8 py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 transition-all">
                            Review
                        </button>
                        <button wire:click="submit" class="order-1 sm:order-2 bg-stone-900 text-white px-8 py-4 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-xl shadow-stone-900/20 hover:bg-primary transition-all">
                            Commit Work
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

