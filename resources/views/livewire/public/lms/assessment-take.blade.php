<div class="min-h-screen bg-stone-50" x-data="{ showConfirmModal: false }">
    @if($state === 'intro')
        <!-- Intro Screen -->
        <div class="max-w-4xl mx-auto px-6 py-24">
            <div class="bg-white rounded-[40px] shadow-2xl border border-stone-200 overflow-hidden">
                <div class="bg-stone-900 p-12 text-white relative">
                    <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 rounded-2xl bg-primary/20 text-primary flex items-center justify-center mb-8 shadow-inner">
                            <span class="material-symbols-outlined text-3xl">psychology</span>
                        </div>
                        <h1 class="font-headline text-4xl md:text-6xl font-bold italic mb-4 leading-tight">{{ $assessment->title }}</h1>
                        <p class="text-stone-400 text-base md:text-xl font-light italic leading-relaxed">{{ $assessment->description ?: 'Scholarly challenge for unit mastery.' }}</p>
                    </div>
                </div>

                <div class="p-12 space-y-12">
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="p-6 rounded-3xl bg-stone-50 border border-stone-100 flex flex-col items-center text-center">
                            <span class="material-symbols-outlined text-stone-300 text-3xl mb-4">timer</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-1">Duration</span>
                            <span class="text-xl font-bold text-stone-900">{{ $assessment->duration_minutes ?: 'Unlimited' }} Minutes</span>
                        </div>
                        <div class="p-6 rounded-3xl bg-stone-50 border border-stone-100 flex flex-col items-center text-center">
                            <span class="material-symbols-outlined text-stone-300 text-3xl mb-4">quiz</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-1">Questions</span>
                            <span class="text-xl font-bold text-stone-900">{{ $assessment->questions->count() }} Items</span>
                        </div>
                        <div class="p-6 rounded-3xl bg-stone-50 border border-stone-100 flex flex-col items-center text-center">
                            <span class="material-symbols-outlined text-stone-300 text-3xl mb-4">grade</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-1">Passing Mark</span>
                            <span class="text-xl font-bold text-stone-900">{{ $assessment->passing_marks ?: 0 }}% Required</span>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                            <span class="w-8 h-px bg-primary/20"></span>
                            Protocols & Instructions
                        </h3>
                        <div class="prose prose-stone max-w-none text-stone-600 italic leading-relaxed">
                            {!! nl2br(e($assessment->instructions ?: 'Please answer all questions to the best of your ability. Your progress is saved automatically.')) !!}
                        </div>
                    </div>

                    <div class="pt-8 border-t border-stone-100 flex justify-center">
                        <button wire:click="startTaking" class="bg-primary text-white px-12 py-5 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-2xl shadow-primary/30 hover:-translate-y-1 transition-all flex items-center gap-4 group">
                            Begin Scholarly Challenge
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($state === 'taking')
        <!-- Active Test UI -->
        <div class="flex flex-col h-screen overflow-hidden">
            <!-- Test Header -->
            <header class="bg-white border-b border-stone-200 px-8 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-6">
                    <div class="w-10 h-10 rounded-xl bg-stone-100 flex items-center justify-center text-stone-500">
                        <span class="material-symbols-outlined">psychology</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-stone-900 italic truncate max-w-xs">{{ $assessment->title }}</h2>
                        <p class="text-[8px] font-bold uppercase tracking-widest text-stone-400">Question {{ $currentQuestionIndex + 1 }} of {{ $assessment->questions->count() }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-8">
                    @if($timeLeft !== -1)
                        <div class="flex items-center gap-4 bg-stone-900 text-white px-6 py-3 rounded-2xl shadow-2xl border border-white/5" 
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
                            <span class="material-symbols-outlined text-base text-orange-500" :class="time < 60 ? 'animate-pulse' : ''">timer</span>
                            <span class="text-xl font-mono font-black tracking-tighter text-orange-500" x-text="format(time)"></span>
                        </div>
                    @endif

                    <button @click="showConfirmModal = true" class="bg-primary text-white px-8 py-2.5 rounded-xl font-bold uppercase tracking-widest text-[9px] shadow-lg shadow-primary/20 hover:scale-105 transition-all">
                        Submit Work
                    </button>
                </div>
            </header>

            <div class="flex-1 flex overflow-hidden">
                <!-- Navigation Sidebar -->
                <aside class="w-80 bg-white border-r border-stone-100 p-8 flex flex-col gap-8 shrink-0 overflow-y-auto no-scrollbar hidden lg:flex">
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
                <main class="flex-1 overflow-y-auto p-8 md:p-12 flex flex-col items-center custom-scrollbar">
                    <div class="w-full max-w-3xl space-y-4">
                        @php $currentQuestion = $assessment->questions[$currentQuestionIndex]; @endphp
                        
                        <div class="space-y-6">
                            <div class="flex items-start gap-6">
                                <span class="w-10 h-10 rounded-xl bg-stone-100 text-stone-400 flex items-center justify-center font-headline text-lg font-bold italic shrink-0">
                                    {{ $currentQuestionIndex + 1 }}
                                </span>
                                <h3 class="font-headline text-xl md:text-3xl font-bold italic text-stone-900 leading-tight pt-1">
                                    {{ $currentQuestion->question_text }}
                                </h3>
                            </div>

                            <div class="grid gap-3 pl-16">
                                @foreach($currentQuestion->options as $option)
                                    <button wire:click="selectOption({{ $currentQuestion->id }}, {{ $option->id }})" 
                                            class="w-full text-left p-4 rounded-xl border transition-all flex items-center gap-4 group
                                            {{ (isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] == $option->id) ? 'bg-stone-900 border-stone-900 text-white shadow-lg translate-x-1' : 'bg-white border-stone-200 text-stone-600 hover:border-primary hover:bg-stone-50' }}">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 
                                            {{ (isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] == $option->id) ? 'border-primary bg-primary text-white' : 'border-stone-200 group-hover:border-primary' }}">
                                            @if(isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] == $option->id)
                                                <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                            @endif
                                        </div>
                                        <span class="text-sm md:text-base font-medium italic">{{ $option->option_text }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer Navigation -->
                        <div class="pt-2 flex items-center justify-between">
                            <button wire:click="prevQuestion" 
                                    @if($currentQuestionIndex === 0) disabled @endif
                                    class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-stone-500 bg-white border border-stone-100 px-6 py-3 rounded-2xl shadow-sm hover:border-primary hover:text-primary disabled:opacity-0 transition-all group">
                                <span class="material-symbols-outlined text-base group-hover:-translate-x-1 transition-transform">arrow_back</span>
                                Previous Question
                            </button>

                            <div class="flex items-center gap-2 hidden md:flex">
                                @foreach($assessment->questions as $idx => $q)
                                    <div class="w-1.5 h-1.5 rounded-full transition-all duration-300 {{ $currentQuestionIndex === $idx ? 'w-6 bg-primary' : (isset($answers[$q->id]) ? 'bg-stone-900' : 'bg-stone-200') }}"></div>
                                @endforeach
                            </div>

                            @if($currentQuestionIndex < $assessment->questions->count() - 1)
                                <button wire:click="nextQuestion" class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-primary bg-white border border-stone-100 px-6 py-3 rounded-2xl shadow-sm hover:border-primary hover:bg-primary hover:text-white transition-all group">
                                    Next Question
                                    <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </button>
                            @else
                                <button @click="showConfirmModal = true" class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-white bg-emerald-600 px-6 py-3 rounded-2xl shadow-lg shadow-emerald-600/20 hover:scale-105 transition-all">
                                    Complete Work
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
             class="fixed inset-0 z-[150] flex items-center justify-center p-6" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-md" @click="showConfirmModal = false"></div>
            
            <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg relative z-10 overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="bg-stone-900 p-10 text-white text-center relative">
                    <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                    <div class="relative z-10">
                        <div class="w-20 h-20 rounded-3xl bg-primary/20 text-primary flex items-center justify-center mx-auto mb-6 shadow-inner">
                            <span class="material-symbols-outlined text-4xl">inventory_2</span>
                        </div>
                        <h3 class="font-headline text-3xl font-bold italic mb-2">Finalize Assessment?</h3>
                        <p class="text-stone-400 text-sm italic">You are about to commit your responses to the permanent archives. This action is irreversible.</p>
                    </div>
                </div>
                
                <div class="p-10 space-y-8">
                    <div class="flex items-center justify-between p-6 rounded-2xl bg-stone-50 border border-stone-100">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Questions Answered</span>
                            <span class="text-xl font-bold text-stone-900">{{ count($answers) }} of {{ $assessment->questions->count() }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Status</span>
                            <span class="text-xs font-bold text-primary italic uppercase tracking-widest">Ready for Review</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button @click="showConfirmModal = false" class="px-8 py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 transition-all">
                            Continue Review
                        </button>
                        <button wire:click="submit" class="bg-primary text-white px-8 py-4 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-xl shadow-primary/20 hover:-translate-y-1 transition-all">
                            Commit Work
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

