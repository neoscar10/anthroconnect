<div class="min-h-screen bg-stone-50 pb-32">
    <!-- Results Hero -->
    <div class="bg-stone-900 pt-24 pb-48 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-12">
                <div class="space-y-6">
                    <nav class="flex items-center gap-2 text-[10px] uppercase font-bold tracking-[0.2em] text-stone-500 mb-8">
                        <a href="{{ route('modules.show', $attempt->assessment->module->slug) }}" class="hover:text-primary transition-colors">Module Home</a>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <span class="text-stone-300">Assessment Results</span>
                    </nav>
                    
                    <h1 class="font-headline text-4xl md:text-6xl text-white font-bold italic leading-tight">{{ $attempt->assessment->title }}</h1>
                    <p class="text-stone-400 text-lg md:text-xl font-light italic">Your performance record in the scholarly archives.</p>
                </div>

                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[40px] p-10 flex items-center gap-10 shadow-2xl">
                    <div class="relative w-32 h-32 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5" />
                            <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="364.4" stroke-dashoffset="{{ 364.4 - (min(100, $attempt->percentage) / 100 * 364.4) }}" class="{{ $attempt->passed ? 'text-primary' : 'text-error' }} transition-all duration-1000" />
                        </svg>
                        <div class="absolute flex flex-col items-center">
                            <span class="text-3xl font-headline font-bold text-white">{{ round($attempt->percentage) }}%</span>
                            <span class="text-[8px] font-bold uppercase tracking-widest text-stone-500">Mastery</span>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full {{ $attempt->passed ? 'bg-primary' : 'bg-error' }}"></div>
                            <span class="text-xl font-headline font-bold italic text-white">{{ $attempt->summary }}</span>
                        </div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Archival Score: {{ $attempt->score }} / {{ $attempt->assessment->questions->sum('marks') }} Marks</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Content -->
    <div class="max-w-4xl mx-auto px-6 -mt-32 relative z-20 space-y-8">
        <!-- Stats Bar -->
        <div class="bg-white rounded-[32px] border border-stone-200 shadow-xl p-8 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="space-y-1 text-center border-r border-stone-100">
                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400">Time Taken</span>
                <p class="text-lg font-bold text-stone-900 italic">{{ $attempt->formatted_duration }}</p>
            </div>
            <div class="space-y-1 text-center border-r border-stone-100">
                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400">Accuracy</span>
                <p class="text-lg font-bold text-stone-900 italic">{{ round(($attempt->answers->where('is_correct', true)->count() / max(1, $attempt->answers->count())) * 100) }}%</p>
            </div>
            <div class="space-y-1 text-center border-r border-stone-100">
                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400">Questions</span>
                <p class="text-lg font-bold text-stone-900 italic">{{ $attempt->answers->count() }} / {{ $attempt->assessment->questions->count() }}</p>
            </div>
            <div class="space-y-1 text-center">
                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400">Verdict</span>
                <p class="text-lg font-bold {{ $attempt->passed ? 'text-primary' : 'text-error' }} italic">{{ $attempt->passed ? 'Qualified' : 'Requires Revision' }}</p>
            </div>
        </div>

        @if($attempt->assessment->show_correct_answers)
            <!-- Detailed Review -->
            <div class="space-y-8">
                <h3 class="font-headline text-3xl font-bold italic text-stone-900 flex items-center gap-4">
                    Scholarly Review
                    <div class="h-px flex-1 bg-stone-200"></div>
                </h3>

                <div class="space-y-6">
                    @foreach($attempt->assessment->questions as $index => $question)
                        @php 
                            $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                            $correctOption = $question->options->where('is_correct', true)->first();
                        @endphp
                        
                        <div class="bg-white rounded-[32px] border border-stone-200 p-6 space-y-4 shadow-sm overflow-hidden relative">
                            @if($userAnswer && $userAnswer->is_correct)
                                <div class="absolute top-0 right-0 w-24 h-24 -mr-12 -mt-12 bg-primary/5 rounded-full flex items-end justify-start p-6">
                                    <span class="material-symbols-outlined text-primary text-2xl">check_circle</span>
                                </div>
                            @elseif($userAnswer)
                                <div class="absolute top-0 right-0 w-24 h-24 -mr-12 -mt-12 bg-error/5 rounded-full flex items-end justify-start p-6">
                                    <span class="material-symbols-outlined text-error text-2xl">cancel</span>
                                </div>
                            @endif

                            <div class="flex items-start gap-4">
                                <span class="w-8 h-8 rounded-lg bg-stone-50 text-stone-400 flex items-center justify-center font-headline font-bold italic shrink-0 border border-stone-100 text-sm">
                                    {{ $index + 1 }}
                                </span>
                                <div class="space-y-1">
                                    <h4 class="text-lg md:text-xl font-bold text-stone-900 italic leading-tight">{{ $question->question_text }}</h4>
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-stone-400">Question Protocol #{{ $question->id }}</span>
                                </div>
                            </div>

                            <div class="grid gap-2">
                                @foreach($question->options as $option)
                                    @php
                                        $isUserChoice = $userAnswer && $userAnswer->selected_option_id == $option->id;
                                        $isCorrect = $option->is_correct;
                                    @endphp
                                    <div class="p-3 rounded-xl border flex items-center justify-between gap-4 transition-all
                                        {{ $isCorrect ? 'bg-primary/5 border-primary/30' : ($isUserChoice ? 'bg-error/5 border-error/20' : 'bg-stone-50 border-stone-100 opacity-60') }}">
                                        
                                        <div class="flex items-center gap-3">
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0
                                                {{ $isCorrect ? 'border-primary bg-primary text-white' : ($isUserChoice ? 'border-error bg-error text-white' : 'border-stone-200') }}">
                                                @if($isCorrect)
                                                    <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                                @elseif($isUserChoice)
                                                    <span class="material-symbols-outlined text-[12px] font-bold">close</span>
                                                @endif
                                            </div>
                                            <span class="text-sm md:text-base font-medium italic {{ $isCorrect ? 'text-stone-900 font-bold' : 'text-stone-500' }}">
                                                {{ $option->option_text }}
                                            </span>
                                        </div>

                                        @if($isUserChoice)
                                            <span class="text-[7px] font-bold uppercase tracking-widest {{ $isCorrect ? 'text-primary' : 'text-error' }}">Your Selection</span>
                                        @elseif($isCorrect)
                                            <span class="text-[7px] font-bold uppercase tracking-widest text-primary">Correct Response</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if($question->explanation)
                                <div class="p-4 rounded-xl bg-stone-100 border border-stone-200 flex gap-3">
                                    <span class="material-symbols-outlined text-stone-400 text-xs shrink-0">info</span>
                                    <div class="space-y-0.5">
                                        <h5 class="text-[8px] font-bold uppercase tracking-widest text-stone-500">Theoretical Rationale</h5>
                                        <p class="text-xs text-stone-600 italic leading-relaxed">{{ $question->explanation }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row items-center justify-center gap-6 pt-12">
            <a href="{{ route('modules.show', $attempt->assessment->module->slug) }}" class="bg-stone-900 text-white px-10 py-5 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-2xl hover:-translate-y-1 transition-all flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Return to Module
            </a>
            
            @if($attempt->assessment->allow_retake || !$attempt->passed)
                <a href="{{ route('assessment.take', $attempt->assessment->id) }}" class="bg-primary text-white px-10 py-5 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-2xl shadow-primary/30 hover:-translate-y-1 transition-all flex items-center gap-3">
                    Attempt Revision
                    <span class="material-symbols-outlined text-sm">refresh</span>
                </a>
            @endif
        </div>
    </div>
</div>
