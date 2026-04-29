<div class="min-h-screen bg-stone-50 py-12" 
    x-data="{ 
        timer: @entangle('time_spent_seconds'),
        targetMinutes: @entangle('target_time_minutes'),
        isStarted: @entangle('is_started'),
        isSubmitted: @entangle('is_submitted'),
        interval: null,
        showConfirm: false,
        showSubmitConfirm: false,
        showRetakeConfirm: false,
        showModelAnswerModal: false,
        showModelAnswerConfirm: false,
        easyMDE: null,
        
        viewModelAnswer() {
            if (this.isSubmitted) {
                this.showModelAnswerModal = true;
            } else {
                this.showModelAnswerConfirm = true;
            }
        },
        downloadPDF() {
            window.print();
        },
        
        initEditor() {
            if (this.isSubmitted) {
                if (this.easyMDE) {
                    this.easyMDE.toTextArea();
                    this.easyMDE = null;
                }
                return;
            };
            
            if (this.easyMDE) {
                this.easyMDE.value(@js($answer_text));
                return;
            }

            this.easyMDE = new EasyMDE({
                element: this.$refs.editor,
                spellChecker: false,
                autosave: { enabled: false },
                placeholder: 'Start typing your scholarly response here...',
                status: false,
                toolbar: [
                    'bold', 'italic', 'heading', '|', 
                    'quote', 'unordered-list', 'ordered-list', '|', 
                    'link', 'image', 'table', '|', 
                    'preview', 'side-by-side', 'fullscreen', '|', 
                    'guide'
                ],
                minHeight: '450px',
                initialValue: @js($answer_text)
            });

            this.easyMDE.codemirror.on('change', () => {
                @this.set('answer_text', this.easyMDE.value(), false);
            });

            // Monitor locking state via Alpine
            this.$watch('isStarted', value => {
                if (this.easyMDE) {
                    this.easyMDE.codemirror.setOption('readOnly', !value);
                }
            });
            
            // Initial state
            if (this.easyMDE) {
                this.easyMDE.codemirror.setOption('readOnly', !this.isStarted);
            }
        },
        startTimer() {
            if (this.isStarted && !this.isSubmitted) {
                this.interval = setInterval(() => {
                    this.timer++;
                    if (this.timer % 60 === 0) {
                        $wire.incrementTimer(60);
                    }
                }, 1000);
            }
        },
        stopTimer() {
            if (this.interval) clearInterval(this.interval);
        },
        confirmStart() {
            if (!{{ auth()->check() ? 'true' : 'false' }}) {
                window.location.href = "{{ route('login') }}";
                return;
            }
            this.showConfirm = true;
        },
        executeStart() {
            this.showConfirm = false;
            $wire.startExam().then(() => {
                this.startTimer();
                if (this.easyMDE) {
                    this.easyMDE.codemirror.setOption('readOnly', false);
                    this.easyMDE.codemirror.focus();
                }
            });
        },
        confirmSubmit() {
            this.showSubmitConfirm = true;
        },
        executeSubmit() {
            this.showSubmitConfirm = false;
            $wire.submitAnswer().then(() => {
                this.stopTimer();
            });
        },
        confirmRetake() {
            this.showRetakeConfirm = true;
        },
        executeRetake() {
            this.showRetakeConfirm = false;
            $wire.retakeExam();
        },
        formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return [
                h > 0 ? h : null,
                m,
                s
            ].filter(v => v !== null).map(v => v < 10 ? '0' + v : v).join(':');
        },
        timeLeft() {
            const total = this.targetMinutes * 60;
            const left = total - this.timer;
            return left > 0 ? left : 0;
        },
        wordCount(text) {
            const clean = text.replace(/<\/?[^>]+(>|$)/g, '').trim();
            return clean ? clean.split(/\s+/).length : 0;
        }
    }" 
    x-init="
        initEditor();
        if(isStarted) startTimer();
        $watch('isStarted', value => {
            if (!value) stopTimer();
        });
        window.addEventListener('exam-reset', (e) => {
            const newAnswer = e.detail[0]?.answer || '';
            if (easyMDE) {
                easyMDE.value(newAnswer);
            }
            initEditor();
        });
    "
>
    <!-- EasyMDE CSS -->
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

    <style>
        .EasyMDEContainer { border: none !important; }
        .editor-toolbar { 
            border: none !important; 
            border-bottom: 1px solid #f3f4f6 !important; 
            background: #f9fafb !important;
            padding: 12px 20px !important;
            border-top-left-radius: 2rem !important;
            border-top-right-radius: 2rem !important;
        }
        .CodeMirror { 
            border: none !important; 
            padding: 20px 40px !important;
            font-family: 'Public Sans', sans-serif !important;
            font-size: 1.125rem !important;
            line-height: 1.75 !important;
        }
        .CodeMirror-focused { box-shadow: none !important; }
        .editor-statusbar { display: none !important; }
        .editor-toolbar button.active, .editor-toolbar button:hover {
            background: #9e50151a !important;
            border-color: transparent !important;
        }
        /* Style the Markdown Preview */
        .editor-preview, .editor-preview-side {
            background: white !important;
            font-family: 'Public Sans', sans-serif !important;
            padding: 40px !important;
        }
        .editor-preview h1, .editor-preview-side h1 { font-size: 2.25rem; font-weight: 800; margin-bottom: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; font-family: 'Lora', serif; font-style: italic; }
        .editor-preview h2, .editor-preview-side h2 { font-size: 1.875rem; font-weight: 700; margin-bottom: 0.75rem; font-family: 'Lora', serif; font-style: italic; }
        .editor-preview h3, .editor-preview-side h3 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; font-family: 'Lora', serif; font-style: italic; }
        .editor-preview strong, .editor-preview-side strong { font-weight: 800; color: #1c1917; }
        .editor-preview p, .editor-preview-side p { margin-bottom: 1.25rem; line-height: 1.8; color: #44403c; }
        .editor-preview ul, .editor-preview-side ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1.25rem; }
        .editor-preview ol, .editor-preview-side ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1.25rem; }
        .editor-preview blockquote, .editor-preview-side blockquote { border-left: 4px solid #9a3412; padding-left: 1.5rem; font-style: italic; color: #57534e; margin: 1.5rem 0; }

        @media print {
            /* Hide the body background and default UI */
            body { 
                visibility: hidden; 
                background: white !important;
            }
            
            /* Collapse the main layout to prevent ghost pages, but keep it visible for the child */
            .min-h-screen, main, .grid {
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Ensure the printable container is visible and positioned correctly */
            #printable-model-answer {
                visibility: visible !important;
                display: block !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: auto !important;
                background: white !important;
                color: black !important;
                padding: 40px !important;
            }

            #printable-model-answer * {
                visibility: visible !important;
            }

            /* Scholarly formatting for print */
            .prose { max-width: none !important; color: black !important; }
            .no-print { display: none !important; }
            
            @page { margin: 1cm; }
        }
    </style>

    <main class="max-w-[1400px] mx-auto w-full px-6 lg:px-20">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-stone-900 font-headline mb-2">UPSC Anthropology Answer Writing Practice</h1>
                <p class="text-stone-600 text-lg">Hone your scholarly writing style with real-time feedback and peer review.</p>
            </div>
            @if($allSubmissions->count() > 0)
                <div class="flex flex-col items-end">
                    <span class="text-[8px] uppercase font-bold text-stone-400 tracking-widest">Selected Session</span>
                    <span class="text-xs font-bold text-orange-800 italic">Attempt #{{ $attempts_count }} {{ $is_submitted ? '(Final)' : '(Working Draft)' }}</span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Content Area -->
            <div class="lg:col-span-8 flex flex-col gap-6 relative">
                <!-- Question Section -->
                <section class="bg-white border border-orange-800/10 rounded-2xl p-8 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
                        <span class="material-symbols-outlined text-7xl">quiz</span>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            @if($question->is_question_of_day)
                                <span class="bg-orange-800 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Question of the Day</span>
                            @endif
                            <span class="text-stone-400 text-[10px] font-bold uppercase tracking-widest">• Paper I, Topic: Cultural Anthropology, Year: {{ $question->year ?: 'General' }}</span>
                        </div>
                        <h3 class="text-2xl font-headline font-bold text-stone-800 leading-snug">
                            {!! strip_tags($question->question_text) !!}
                        </h3>
                    </div>
                </section>

                <!-- Timer & Tools -->
                <div class="flex flex-wrap items-center justify-between gap-4 bg-orange-800/5 p-5 rounded-2xl border border-orange-800/10">
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-500">Duration:</span>
                            <div class="flex gap-2">
                                @foreach([5, 10, 15, 20, 30] as $min)
                                    <button 
                                        @click="targetMinutes = {{ $min }}" 
                                        :disabled="isStarted || isSubmitted"
                                        class="px-4 py-1.5 rounded-xl text-[10px] font-bold transition-all uppercase tracking-widest border"
                                        :class="targetMinutes == {{ $min }} ? 'bg-orange-800 text-white border-orange-800 shadow-md' : 'bg-white border-orange-800/20 text-stone-600 hover:bg-orange-800 hover:text-white'"
                                    >
                                        {{ $min }}m
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @if(!$is_submitted)
                            <div class="h-6 w-px bg-stone-200"></div>
                            <button x-show="!isStarted" @click="confirmStart()" class="px-8 py-3 bg-stone-900 text-white font-bold rounded-xl hover:bg-stone-800 transition-all shadow-lg shadow-stone-900/10 flex items-center gap-2 active:scale-95 text-[10px] uppercase tracking-widest">
                                <span class="material-symbols-outlined text-sm">{{ $time_spent_seconds > 0 ? 'resume' : 'play_circle' }}</span>
                                {{ $time_spent_seconds > 0 ? 'Resume Practice' : 'Start Practice' }}
                            </button>
                            <div x-show="isStarted" class="px-6 py-2 bg-orange-100 text-orange-800 font-bold rounded-xl text-[10px] uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 bg-orange-800 rounded-full animate-pulse"></span>
                                Session Active
                            </div>
                        @else
                            <div class="h-6 w-px bg-stone-200"></div>
                            <button @click="confirmRetake()" class="px-8 py-3 bg-orange-800 text-white font-bold rounded-xl hover:bg-orange-900 transition-all shadow-lg shadow-orange-900/10 flex items-center gap-2 active:scale-95 text-[10px] uppercase tracking-widest">
                                <span class="material-symbols-outlined text-sm">restart_alt</span>
                                Start New Attempt
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex flex-col items-end">
                            <span class="text-[8px] uppercase font-bold text-stone-400 tracking-widest">Time Remaining</span>
                            <div class="flex items-center gap-2 text-orange-800 font-mono text-2xl font-bold">
                                <span class="material-symbols-outlined text-xl">timer</span>
                                <span x-text="formatTime(timeLeft())"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editor Area -->
                <div class="relative bg-white border border-stone-200 rounded-[2rem] shadow-xl flex flex-col min-h-[600px] overflow-hidden transition-all" :class="!isStarted && !isSubmitted ? 'opacity-50 grayscale-[0.5]' : 'focus-within:ring-2 focus-within:ring-orange-800/20'">
                    <!-- Text Area / Markdown Editor -->
                    <div class="flex-1 relative" wire:ignore>
                        @if($is_submitted)
                            <div class="absolute inset-0 p-10 bg-stone-50 overflow-y-auto prose prose-stone max-w-none">
                                {!! Str::markdown($answer_text) !!}
                            </div>
                        @else
                            <textarea x-ref="editor" class="hidden"></textarea>
                            
                            <!-- Alpine-managed Overlay -->
                            <div x-show="!isStarted" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-stone-100/10 backdrop-blur-[2px] cursor-not-allowed pointer-events-none">
                                <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">lock</span>
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400">Editor Locked</p>
                            </div>
                        @endif
                    </div>

                    <!-- Status Bar -->
                    <div class="flex items-center justify-between px-8 py-4 border-t border-stone-100 text-[10px] text-stone-500 font-bold uppercase tracking-widest bg-stone-50/30">
                        <div class="flex gap-6">
                            <span class="flex items-center gap-1.5"><span class="text-stone-900" x-text="wordCount($wire.answer_text)"></span> Words</span>
                            <span class="flex items-center gap-1.5"><span class="text-stone-900" x-text="$wire.answer_text.length"></span> Characters</span>
                        </div>
                        <div class="flex gap-4 items-center">
                            @if($last_saved_at)
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                    <span>Last synced: {{ $last_saved_at }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    @if($is_started && !$is_submitted)
                        <button wire:click="saveDraft" wire:loading.attr="disabled" class="group px-8 py-3.5 border-2 border-orange-800/20 text-orange-800 font-bold rounded-2xl hover:bg-orange-800 transition-all hover:text-white text-xs uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">save</span>
                            Save & Lock Draft
                        </button>
                        <button @click="confirmSubmit()" class="px-10 py-3.5 bg-orange-800 text-white font-bold rounded-2xl hover:bg-orange-900 transition-all shadow-xl shadow-orange-900/20 text-xs uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">send</span>
                            Submit Final Answer
                        </button>
                    @elseif($is_submitted)
                        <div class="px-8 py-3.5 bg-green-50 text-green-700 font-bold rounded-2xl border border-green-100 text-xs uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Response Submitted
                        </div>
                    @endif
                </div>

                <!-- High-Impact Model Answer Access -->
                @if($question->model_answer)
                    <div class="mt-12">
                        <button @click="viewModelAnswer()" 
                            class="w-full flex items-center justify-between p-8 rounded-[2.5rem] transition-all shadow-xl group overflow-hidden relative text-left"
                            :class="isSubmitted ? 'bg-stone-900 text-white hover:bg-stone-800' : 'bg-white border border-stone-200 hover:border-orange-800/30'"
                        >
                            <div class="flex items-center gap-6 relative z-10">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center transition-all duration-500"
                                    :class="isSubmitted ? 'bg-orange-800 shadow-lg shadow-orange-900/40 scale-110' : 'bg-stone-50 group-hover:bg-orange-800/10'"
                                >
                                    <span class="material-symbols-outlined text-3xl transition-colors duration-500"
                                        :class="isSubmitted ? 'text-white' : 'text-stone-300 group-hover:text-orange-800'"
                                    >verified</span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold font-headline italic leading-tight"
                                        :class="isSubmitted ? 'text-white' : 'text-stone-800'"
                                    >
                                        <span x-text="isSubmitted ? 'View Model Answer & Structure' : 'Review Model Answer'"></span>
                                    </h4>
                                    <p class="text-xs font-bold uppercase tracking-widest mt-2 opacity-60">
                                        <span x-text="isSubmitted ? 'Recommended scholarly response' : 'Submit your attempt to unlock the full response'"></span>
                                    </p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-3xl transition-all relative z-10"
                                :class="isSubmitted ? 'text-orange-800' : 'text-stone-200 group-hover:text-orange-800 group-hover:translate-x-2'"
                            >arrow_forward</span>
                            
                            <!-- Subtle background glow for submitted state -->
                            <div x-show="isSubmitted" class="absolute top-0 right-0 w-64 h-64 bg-orange-800/20 blur-[100px] -mr-32 -mt-32"></div>
                        </button>
                    </div>
                @endif

                <!-- Feedback and Discussion -->
                <section class="mt-8">
                    <div class="flex border-b border-stone-200 mb-8">
                        <button class="px-8 py-4 border-b-2 border-orange-800 text-orange-800 font-bold text-xs uppercase tracking-widest">Expert Evaluation</button>
                        <button class="px-8 py-4 text-stone-400 font-bold text-xs uppercase tracking-widest hover:text-orange-800 transition-colors">Peer Review (Coming Soon)</button>
                    </div>
                    <div class="flex flex-col gap-4">
                        @if($is_submitted && $submission && $submission->evaluated_at)
                            <div class="p-8 bg-stone-900 text-white rounded-[2rem] border border-stone-800 relative overflow-hidden shadow-2xl">
                                <div class="absolute top-0 right-0 p-8 opacity-10">
                                    <span class="material-symbols-outlined text-7xl text-orange-800">verified</span>
                                </div>
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-800/20 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-orange-800">school</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-white">Faculty Evaluation</h4>
                                        <p class="text-[10px] text-stone-400 uppercase font-bold tracking-widest">Evaluated on {{ $submission->evaluated_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="ml-auto text-center">
                                        <div class="text-3xl font-black text-orange-800 font-headline">{{ $submission->score }}</div>
                                        <div class="text-[8px] font-bold uppercase tracking-widest text-stone-500">Score / {{ $submission->question->marks ?: 100 }}</div>
                                    </div>
                                </div>
                                <div class="prose prose-invert prose-sm max-w-none text-stone-300 leading-relaxed italic">
                                    {{ $submission->feedback_text }}
                                </div>
                            </div>
                        @else
                            <div class="p-10 bg-stone-100/50 rounded-[2rem] border border-stone-200 border-dashed flex flex-col items-center text-center">
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-3xl text-stone-300 animate-pulse">
                                        {{ $is_submitted ? 'query_stats' : 'history_edu' }}
                                    </span>
                                </div>
                                <h4 class="text-lg font-bold text-stone-800 font-headline italic mb-2">
                                    {{ $is_submitted ? 'Expert Evaluation Pending' : 'Scholarly Review Awaiting' }}
                                </h4>
                                <p class="text-stone-500 text-sm max-w-xs leading-relaxed">
                                    {{ $is_submitted 
                                        ? 'Our senior faculty is currently reviewing your submission. You will receive a detailed report covering conceptual clarity and scholarly depth shortly.' 
                                        : 'Complete your practice attempt and submit it for a professional evaluation based on standard UPSC parameters.' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Restriction Overlay -->
                @if($restriction['locked'])
                    <div class="absolute inset-0 z-[60] flex items-center justify-center p-12 text-center rounded-[2.5rem] backdrop-blur-md bg-stone-50/40">
                        <div class="max-w-md bg-white p-12 rounded-[3rem] shadow-2xl border border-stone-200">
                            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-8 text-orange-800">
                                <span class="material-symbols-outlined text-4xl">lock</span>
                            </div>
                            <h2 class="text-3xl font-headline italic font-bold text-stone-900 mb-4">Member Exclusive</h2>
                            <p class="text-stone-600 mb-10 leading-relaxed">
                                This practice question and evaluation criteria are reserved for our active community members. Join us to unlock all premium resources.
                            </p>
                            
                            @if($restriction['cta'] === 'login')
                                <a wire:navigate href="{{ route('login') }}" class="block w-full py-5 bg-stone-900 text-white font-bold rounded-2xl shadow-xl hover:-translate-y-1 transition-all uppercase tracking-widest text-xs">
                                    Log In to Access
                                </a>
                            @else
                                <button @click="$dispatch('open-upgrade-modal')" class="w-full py-5 bg-orange-800 text-white font-bold rounded-2xl shadow-xl shadow-orange-900/20 hover:-translate-y-1 transition-all uppercase tracking-widest text-xs">
                                    Upgrade Membership
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar Area -->
            <aside class="lg:col-span-4 flex flex-col gap-8">
                <!-- Practice Stats -->
                <div class="bg-white border border-stone-200 rounded-[2rem] p-8 shadow-sm">
                    <h4 class="text-sm font-bold text-stone-400 uppercase tracking-[0.2em] mb-6">Practice History</h4>
                    
                    <div class="space-y-3 mb-6 max-h-[300px] overflow-y-auto no-scrollbar pr-1">
                        @foreach($allSubmissions as $sub)
                            <button 
                                wire:click="selectSubmission({{ $sub->id }})" 
                                class="w-full flex items-center justify-between p-4 rounded-2xl border transition-all text-left"
                                :class="$wire.active_submission_id == {{ $sub->id }} ? 'bg-orange-800 border-orange-800 text-white shadow-lg' : 'bg-stone-50 border-stone-100 text-stone-600 hover:border-orange-800/30'"
                            >
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-widest opacity-60">Attempt #{{ $sub->attempts_count }}</div>
                                    <div class="text-xs font-bold">{{ $sub->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($sub->status === 'submitted')
                                        @if($sub->evaluated_at)
                                            <span class="text-xs font-black">{{ $sub->score }}/{{ $question->marks ?: 100 }}</span>
                                        @else
                                            <span class="material-symbols-outlined text-sm">schedule</span>
                                        @endif
                                    @else
                                        <span class="material-symbols-outlined text-sm">edit_note</span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <div class="h-px bg-stone-100 mb-6"></div>

                    <div class="flex items-center gap-6">
                        <div class="flex flex-col">
                            <span class="text-3xl font-black text-stone-900 font-headline">{{ $allSubmissions->count() }}</span>
                            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Total Attempts</span>
                        </div>
                        <div class="w-px h-10 bg-stone-100"></div>
                        <div class="flex flex-col">
                            <span class="text-3xl font-black text-orange-800 font-headline">{{ $allSubmissions->where('status', 'submitted')->count() }}</span>
                            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Submissions</span>
                        </div>
                    </div>
                </div>

                <!-- Writing Guidelines -->
                @if($question->answer_guidelines)
                    <div class="bg-orange-800/5 border border-orange-800/10 rounded-[2rem] p-8">
                        <h4 class="text-lg font-bold text-orange-800 font-headline italic mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined">lightbulb</span>
                            Writing Guidelines
                        </h4>
                        <div class="prose prose-stone prose-sm max-w-none text-stone-600 leading-relaxed">
                            {!! Str::markdown($question->answer_guidelines) !!}
                        </div>
                    </div>
                @endif

                <!-- Scoring Criteria -->
                @if(count($question->evaluation_rubric ?? []))
                    <div class="bg-stone-900 border border-stone-800 rounded-[2rem] p-8 text-white shadow-xl">
                        <h4 class="text-lg font-bold text-orange-800 font-headline italic mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined">analytics</span>
                            Scoring Criteria
                        </h4>
                        <div class="space-y-4">
                            @foreach($question->evaluation_rubric as $rubric)
                                <div class="flex justify-between items-start gap-4 pb-3 border-b border-stone-800 last:border-0">
                                    <span class="text-xs text-stone-300 leading-relaxed">{{ $rubric['criteria'] }}</span>
                                    <span class="text-xs font-bold text-orange-800 whitespace-nowrap">{{ $rubric['marks'] }}M</span>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-6 text-[8px] uppercase tracking-[0.2em] text-stone-500 font-bold">Standard UPSC Marking Scheme applied</p>
                    </div>
                @endif

                <!-- Related Resources / Learning Concepts -->
                @if(count($question->learning_resources ?? []))
                    <div class="flex flex-col gap-6">
                        <h4 class="text-lg font-bold text-stone-900 font-headline italic px-4">Learn Concepts</h4>
                        @foreach($question->learning_resources as $res)
                            <a class="group block p-5 bg-white border border-stone-100 rounded-3xl hover:border-orange-800 transition-all shadow-sm" href="{{ $res['url'] }}" target="_blank">
                                <div class="w-full h-32 rounded-2xl bg-stone-50 mb-4 overflow-hidden relative">
                                    <div class="absolute inset-0 bg-gradient-to-br from-orange-800/10 to-orange-800/5 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-orange-800 text-4xl opacity-50 group-hover:scale-110 transition-transform">
                                            {{ $res['type'] === 'video' ? 'play_circle' : ($res['type'] === 'pdf' ? 'description' : 'menu_book') }}
                                        </span>
                                    </div>
                                </div>
                                <h5 class="font-bold text-stone-900 group-hover:text-orange-800 transition-colors leading-tight mb-2">{{ $res['title'] }}</h5>
                                <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest">{{ $res['type'] ?: 'Reference' }} • Linked Resource</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </aside>
        </div>

        <!-- Start Confirmation Modal -->
        <div 
            x-show="showConfirm" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-stone-900/60 backdrop-blur-sm"
            x-cloak
        >
            <div @click.away="showConfirm = false" class="bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl border border-stone-200 text-center">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-8 text-orange-800">
                    <span class="material-symbols-outlined text-4xl">timer</span>
                </div>
                <h2 class="text-3xl font-headline italic font-bold text-stone-900 mb-4">{{ $time_spent_seconds > 0 ? 'Resume Practice?' : 'Ready to Start?' }}</h2>
                <p class="text-stone-600 mb-10 leading-relaxed">
                    @if($time_spent_seconds > 0)
                        You have <span class="font-bold text-orange-800" x-text="formatTime(timeLeft())"></span> remaining for this practice session. The timer will resume as soon as you confirm.
                    @else
                        You have set a target of <span class="font-bold text-orange-800" x-text="targetMinutes"></span> minutes for this practice session. The timer will begin as soon as you confirm.
                    @endif
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <button @click="showConfirm = false" class="py-4 border-2 border-stone-100 text-stone-400 font-bold rounded-2xl hover:bg-stone-50 transition-all uppercase tracking-widest text-[10px]">
                        Not Yet
                    </button>
                    <button @click="executeStart()" class="py-4 bg-stone-900 text-white font-bold rounded-2xl shadow-xl hover:-translate-y-1 transition-all uppercase tracking-widest text-[10px]">
                        {{ $time_spent_seconds > 0 ? 'Resume Now' : 'Start Now' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Confirmation Modal -->
        <div 
            x-show="showSubmitConfirm" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-stone-900/60 backdrop-blur-sm"
            x-cloak
        >
            <div @click.away="showSubmitConfirm = false" class="bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl border border-stone-200 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8 text-green-800">
                    <span class="material-symbols-outlined text-4xl">task_alt</span>
                </div>
                <h2 class="text-3xl font-headline italic font-bold text-stone-900 mb-4">Final Submission?</h2>
                <p class="text-stone-600 mb-10 leading-relaxed">
                    Once submitted, your practice session will be finalized for evaluation. You have spent <span class="font-bold text-stone-900" x-text="formatTime(timer)"></span> on this attempt.
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <button @click="showSubmitConfirm = false" class="py-4 border-2 border-stone-100 text-stone-400 font-bold rounded-2xl hover:bg-stone-50 transition-all uppercase tracking-widest text-[10px]">
                        Review More
                    </button>
                    <button @click="executeSubmit()" class="py-4 bg-green-700 text-white font-bold rounded-2xl shadow-xl hover:-translate-y-1 transition-all uppercase tracking-widest text-[10px]">
                        Submit Final
                    </button>
                </div>
            </div>
        </div>

        <!-- Retake Confirmation Modal -->
        <div 
            x-show="showRetakeConfirm" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-stone-900/60 backdrop-blur-sm"
            x-cloak
        >
            <div @click.away="showRetakeConfirm = false" class="bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl border border-stone-200 text-center">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-8 text-orange-800">
                    <span class="material-symbols-outlined text-4xl">restart_alt</span>
                </div>
                <h2 class="text-3xl font-headline italic font-bold text-stone-900 mb-4">Start New Attempt?</h2>
                <p class="text-stone-600 mb-10 leading-relaxed">
                    This will preserve your current work and open a fresh scholarly attempt. You can switch back to previous attempts at any time using the sidebar.
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <button @click="showRetakeConfirm = false" class="py-4 border-2 border-stone-100 text-stone-400 font-bold rounded-2xl hover:bg-stone-50 transition-all uppercase tracking-widest text-[10px]">
                        Cancel
                    </button>
                    <button @click="executeRetake()" class="py-4 bg-stone-900 text-white font-bold rounded-2xl shadow-xl hover:-translate-y-1 transition-all uppercase tracking-widest text-[10px]">
                        Start Now
                    </button>
                </div>
            </div>
        </div>

        <!-- Model Answer Confirmation Modal -->
        <div 
            x-show="showModelAnswerConfirm" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-stone-900/60 backdrop-blur-sm"
            x-cloak
        >
            <div @click.away="showModelAnswerConfirm = false" class="bg-white rounded-[3rem] p-10 max-w-lg w-full shadow-2xl border border-stone-200 text-center">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-8 text-orange-800">
                    <span class="material-symbols-outlined text-4xl">warning</span>
                </div>
                <h2 class="text-3xl font-headline italic font-bold text-stone-900 mb-4">View without Attempt?</h2>
                <p class="text-stone-600 mb-10 leading-relaxed">
                    Anthropological mastery is built through practice. We strongly encourage you to <span class="font-bold text-stone-900">submit an attempt</span> before viewing the model answer to maximize your learning.
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <button @click="showModelAnswerConfirm = false" class="py-4 border-2 border-stone-100 text-stone-400 font-bold rounded-2xl hover:bg-stone-50 transition-all uppercase tracking-widest text-[10px]">
                        I'll Practice First
                    </button>
                    <button @click="showModelAnswerConfirm = false; showModelAnswerModal = true" class="py-4 bg-stone-900 text-white font-bold rounded-2xl shadow-xl hover:-translate-y-1 transition-all uppercase tracking-widest text-[10px]">
                        View Anyway
                    </button>
                </div>
            </div>
        </div>

        <!-- Full Model Answer Modal -->
        <div 
            x-show="showModelAnswerModal" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[110] flex items-center justify-center p-6 md:p-12 bg-stone-900/80 backdrop-blur-md"
            x-cloak
        >
            <div @click.away="showModelAnswerModal = false" class="bg-white rounded-[3rem] max-w-5xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-stone-200 overflow-hidden">
                <!-- Modal Header -->
                <div class="px-10 py-8 border-b border-stone-100 flex items-center justify-between bg-stone-50/50">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-orange-800 flex items-center justify-center shadow-lg shadow-orange-900/20">
                            <span class="material-symbols-outlined text-white">verified</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-headline italic font-bold text-stone-900 leading-tight">Model Answer & Scholarly Structure</h2>
                            <p class="text-[10px] text-stone-400 uppercase font-bold tracking-widest mt-1">Recommended Response for {{ $question->year ?: 'General' }} Question</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <button @click="downloadPDF()" class="flex items-center gap-2 px-6 py-3 bg-stone-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-stone-800 transition-all shadow-lg">
                            <span class="material-symbols-outlined text-sm">download</span>
                            Download PDF
                        </button>
                        <button @click="showModelAnswerModal = false" class="w-10 h-10 rounded-full hover:bg-stone-200 flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-stone-500">close</span>
                        </button>
                    </div>
                </div>

                <!-- Modal Body (Printable Content) -->
                <div class="flex-1 overflow-y-auto p-12 no-scrollbar" id="printable-model-answer">
                    <div class="max-w-3xl mx-auto">
                        <!-- Branding for Print -->
                        <div class="hidden print:block mb-10 border-b-2 border-stone-900 pb-6">
                            <h1 class="text-3xl font-headline italic font-bold text-stone-900">AnthroConnect Archivist</h1>
                            <p class="text-[10px] font-bold text-stone-500 uppercase tracking-widest mt-2">Professional Answer Writing Practice • Model Response</p>
                        </div>

                        <!-- Question Context -->
                        <div class="mb-10 p-8 bg-stone-50 rounded-3xl border border-stone-100 print:bg-transparent print:p-0 print:border-none">
                            <span class="text-[8px] font-bold text-stone-400 uppercase tracking-widest mb-2 block">Question Overview</span>
                            <h3 class="text-xl font-bold text-stone-800 leading-snug">
                                {!! strip_tags($question->question_text) !!}
                            </h3>
                        </div>

                        <!-- The Answer -->
                        <div class="prose prose-stone prose-lg max-w-none prose-headings:font-headline prose-headings:italic prose-blockquote:border-orange-800">
                            @if($question->model_answer)
                                {!! Str::markdown($question->model_answer) !!}
                            @else
                                <div class="py-20 text-center text-stone-400 italic">
                                    Model answer content is currently being curated by our senior faculty. Check back soon.
                                </div>
                            @endif
                        </div>

                        <!-- Guidelines in PDF -->
                        @if($question->answer_guidelines)
                            <div class="mt-12 pt-12 border-t border-stone-100">
                                <h4 class="text-lg font-bold font-headline italic text-stone-900 mb-6">Expert Guidelines</h4>
                                <div class="prose prose-stone prose-sm max-w-none text-stone-600">
                                    {!! Str::markdown($question->answer_guidelines) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Footer for Print -->
                        <div class="hidden print:block mt-12 pt-6 border-t border-stone-100 text-[8px] text-stone-400 font-bold uppercase tracking-[0.2em] text-center">
                            © {{ date('Y') }} AnthroConnect Archivist Portal • Scholarly Resource for UPSC Anthropology
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Navigation -->
        <nav class="mt-16 py-12 border-t border-stone-200 flex items-center justify-between">
            @if($prev)
                <a wire:navigate href="{{ route('exams.show', $prev->slug) }}" class="flex items-center gap-3 text-stone-400 hover:text-orange-800 transition-all font-bold uppercase tracking-widest text-[10px]">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Previous Question
                </a>
            @else
                <div></div>
            @endif

            <div class="hidden md:flex gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-800"></span>
                <span class="w-2 h-2 rounded-full bg-stone-200"></span>
                <span class="w-2 h-2 rounded-full bg-stone-200"></span>
            </div>

            @if($next)
                <a wire:navigate href="{{ route('exams.show', $next->slug) }}" class="flex items-center gap-3 text-stone-400 hover:text-orange-800 transition-all font-bold uppercase tracking-widest text-[10px] text-right">
                    Next Question
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            @else
                <div></div>
            @endif
        </nav>
    </main>
</div>
