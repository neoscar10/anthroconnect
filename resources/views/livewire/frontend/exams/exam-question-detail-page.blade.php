<div class="min-h-screen bg-stone-50 py-12" 
    x-data="examPractice({ 
        timer: @entangle('time_spent_seconds'),
        targetMinutes: @entangle('target_time_minutes'),
        isStarted: @entangle('is_started'),
        isSubmitted: @entangle('is_submitted'),
        answerText: @js($answer_text),
        isLoggedIn: @js(auth()->check()),
        loginRoute: @js(route('login'))
    })"
>
    <script>
        function examPractice(config) {
            return {
                timer: config.timer,
                targetMinutes: config.targetMinutes,
                isStarted: config.isStarted,
                isSubmitted: config.isSubmitted,
                interval: null,
                showConfirm: false,
                showSubmitConfirm: false,
                showRetakeConfirm: false,
                showModelAnswerModal: false,
                showModelAnswerConfirm: false,
                isGeneratingPDF: false,
                easyMDE: null,
                
                init() {
                    this.initEditor();
                    if(this.isStarted) this.startTimer();
                    
                    this.$watch('isStarted', value => {
                        if (value) {
                            this.startTimer();
                        } else {
                            this.stopTimer();
                        }
                        
                        if (this.easyMDE) {
                            this.easyMDE.codemirror.setOption('readOnly', !value);
                            if (value) {
                                this.$nextTick(() => {
                                    this.easyMDE.codemirror.focus();
                                });
                            }
                        }
                    });

                    window.addEventListener('exam-reset', (e) => {
                        const newAnswer = e.detail[0]?.answer || '';
                        if (this.easyMDE) {
                            this.easyMDE.value(newAnswer);
                        }
                        this.initEditor();
                    });
                },

                viewModelAnswer() {
                    if (this.isSubmitted) {
                        this.showModelAnswerModal = true;
                    } else {
                        this.showModelAnswerConfirm = true;
                    }
                },
                downloadPDF() {
                    this.isGeneratingPDF = true;
                    const element = document.getElementById('printable-model-answer');
                    
                    if (!element) {
                        console.error('Printable element not found');
                        this.isGeneratingPDF = false;
                        return;
                    }

                    const opt = {
                        margin: [0.2, 0.5],
                        filename: 'AnthroConnect-ModelAnswer-' + @js($slug) + '.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { 
                            scale: 2, 
                            useCORS: true,
                            letterRendering: true,
                            scrollY: 0
                        },
                        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
                    };

                    const container = document.createElement('div');
                    container.style.position = 'fixed';
                    container.style.left = '-9999px';
                    container.style.top = '0';
                    container.appendChild(element.cloneNode(true));
                    container.firstChild.classList.remove('hidden');
                    container.firstChild.style.display = 'block';
                    container.firstChild.style.width = '800px'; 
                    container.firstChild.style.padding = '20px 40px';
                    container.firstChild.style.background = 'white';
                    
                    document.body.appendChild(container);
                    
                    html2pdf().set(opt).from(container.firstChild).save().then(() => {
                        document.body.removeChild(container);
                        this.isGeneratingPDF = false;
                    }).catch(err => {
                        console.error('PDF Generation Error:', err);
                        this.isGeneratingPDF = false;
                    });
                },
                
                initEditor() {
                    if (this.isSubmitted || this.$wire.submission_type === 'file') {
                        if (this.easyMDE) {
                            this.easyMDE.toTextArea();
                            this.easyMDE = null;
                        }
                        return;
                    };
                    
                    if (this.easyMDE) {
                        this.easyMDE.value(config.answerText);
                        this.easyMDE.codemirror.setOption('readOnly', !this.isStarted);
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
                        initialValue: config.answerText
                    });

                    this.easyMDE.codemirror.setOption('readOnly', !this.isStarted);

                    this.easyMDE.codemirror.on('change', () => {
                        this.$wire.set('answer_text', this.easyMDE.value(), false);
                    });
                },
                startTimer() {
                    if (this.isStarted && !this.isSubmitted) {
                        this.interval = setInterval(() => {
                            this.timer++;
                            if (this.timer % 60 === 0) {
                                this.$wire.incrementTimer(60);
                            }
                        }, 1000);
                    }
                },
                stopTimer() {
                    if (this.interval) clearInterval(this.interval);
                },
                confirmStart() {
                    if (!config.isLoggedIn) {
                        window.location.href = config.loginRoute;
                        return;
                    }
                    this.showConfirm = true;
                },
                executeStart() {
                    this.showConfirm = false;
                    this.$wire.startExam().then(() => {
                        // The watch on isStarted will handle the readOnly and timer toggle
                    });
                },
                confirmSubmit() {
                    this.showSubmitConfirm = true;
                },
                executeSubmit() {
                    this.showSubmitConfirm = false;
                    this.$wire.submitAnswer().then(() => {
                        this.stopTimer();
                    });
                },
                confirmRetake() {
                    this.showRetakeConfirm = true;
                },
                executeRetake() {
                    this.showRetakeConfirm = false;
                    this.$wire.retakeExam();
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
                    const clean = text ? text.replace(/<[^>]*>?/gm, '').trim() : '';
                    return clean ? clean.split(/\s+/).length : 0;
                }
            }
        }
    </script>
    <!-- EasyMDE CSS -->
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
        @if($question->question_kind === 'past')
            <!-- READ ONLY VIEW FOR PAST QUESTIONS -->
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h1 class="text-3xl md:text-4xl font-black text-stone-900 font-headline mb-2">UPSC Past Year Question (PYQ)</h1>
                    <p class="text-stone-600 text-lg">Reference model answer and conceptual framework.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 flex flex-col gap-6">
                    <!-- Question Section -->
                    <section class="bg-white border border-stone-200 rounded-2xl p-8 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
                            <span class="material-symbols-outlined text-7xl">archive</span>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="bg-sandstone text-charcoal text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">{{ $question->year }} Paper {{ $question->paper_type ?: 'I' }}</span>
                                <span class="text-stone-400 text-[10px] font-bold uppercase tracking-widest">• {{ $question->marks }} Marks</span>
                            </div>
                            <h3 class="text-2xl font-headline font-bold text-stone-800 leading-snug">
                                {!! $question->question_text !!}
                            </h3>
                        </div>
                    </section>

                    <!-- Model Answer (Always Visible for Past) -->
                    <section class="bg-white text-stone-900 border border-stone-200 rounded-[2.5rem] p-10 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-800/10 blur-[100px] -mr-32 -mt-32"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-orange-800 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="material-symbols-outlined text-white">verified</span>
                                    </div>
                                    <div>
                                        <h4 class="text-2xl font-bold font-headline italic">Model Answer Framework</h4>
                                        <p class="text-[10px] uppercase font-bold tracking-widest text-orange-800">UPSC Standard Reference</p>
                                    </div>
                                </div>
                                <button @click="downloadPDF()" 
                                    class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest bg-stone-50 hover:bg-stone-100 text-stone-600 px-4 py-2 rounded-lg transition-colors disabled:opacity-50"
                                    :disabled="isGeneratingPDF"
                                >
                                    <span x-show="!isGeneratingPDF" class="material-symbols-outlined text-sm">download</span>
                                    <span x-show="isGeneratingPDF" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                    <span x-text="isGeneratingPDF ? 'Generating...' : 'Download Ref'"></span>
                                </button>
                            </div>

                            <div class="prose prose-stone max-w-none prose-orange">
                                @if($question->model_answer)
                                    {!! Str::markdown($question->model_answer) !!}
                                @else
                                    <p class="italic text-stone-500 text-sm">Model answer is currently being prepared by our expert faculty.</p>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-4 flex flex-col gap-8">
                    <!-- Scoring Rubric for Reference -->
                    @if(count($question->evaluation_rubric ?? []))
                        <div class="bg-white border border-stone-200 rounded-[2.5rem] p-8 shadow-sm">
                            <h4 class="text-lg font-bold text-stone-900 font-headline italic mb-6">Marking Parameters</h4>
                            <div class="space-y-4">
                                @foreach($question->evaluation_rubric as $rubric)
                                    <div class="flex justify-between items-start gap-4 pb-3 border-b border-stone-50 last:border-0">
                                        <span class="text-xs text-stone-600">{{ $rubric['criteria'] }}</span>
                                        <span class="text-xs font-bold text-orange-800">{{ $rubric['marks'] }}M</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Answer Guidelines -->
                    @if($question->answer_guidelines)
                        <div class="bg-orange-800/5 border border-orange-800/10 rounded-[2.5rem] p-8">
                            <h4 class="text-lg font-bold text-orange-800 font-headline italic mb-4">Structure Hints</h4>
                            <div class="prose prose-stone prose-sm max-w-none text-stone-600">
                                {!! Str::markdown($question->answer_guidelines) !!}
                            </div>
                        </div>
                    @endif

                    <div class="p-6 bg-stone-50 rounded-2xl border border-stone-200 text-center">
                        <p class="text-sm text-stone-500 italic mb-4">Want to practice this question in exam mode?</p>
                        <button class="w-full py-3 bg-stone-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-orange-800 transition-colors opacity-50 cursor-not-allowed" title="Past questions are currently read-only">Exam Mode Disabled</button>
                    </div>
                </aside>
            </div>
        @else
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

                <!-- Timer & Tools (Visible for Typed OR when Submitted) -->
                <div x-show="$wire.submission_type === 'text' || isSubmitted" class="flex flex-wrap items-center justify-between gap-4 bg-orange-800/5 p-5 rounded-2xl border border-orange-800/10">
                    <div class="flex flex-wrap items-center gap-6">
                        @if(!$is_submitted)
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
                        @endif

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
                            <div class="flex items-center gap-4">
                                <div class="px-4 py-2 bg-green-100 text-green-700 font-bold rounded-xl text-[10px] uppercase tracking-widest flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    Attempt Completed
                                </div>
                                <button @click="confirmRetake()" class="px-8 py-3 bg-orange-800 text-white font-bold rounded-xl hover:bg-orange-900 transition-all shadow-lg shadow-orange-900/10 flex items-center gap-2 active:scale-95 text-[10px] uppercase tracking-widest">
                                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                                    Start New Attempt
                                </button>
                            </div>
                        @endif
                    </div>

                    @if(!$is_submitted)
                        <div class="flex items-center gap-6">
                            <div class="flex flex-col items-end">
                                <span class="text-[8px] uppercase font-bold text-stone-400 tracking-widest">Time Remaining</span>
                                <div class="flex items-center gap-2 text-orange-800 font-mono text-2xl font-bold">
                                    <span class="material-symbols-outlined text-xl">timer</span>
                                    <span x-text="formatTime(timeLeft())"></span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-end">
                            <span class="text-[8px] uppercase font-bold text-stone-400 tracking-widest">Time Invested</span>
                            <div class="flex items-center gap-2 text-stone-900 font-mono text-2xl font-bold">
                                <span class="material-symbols-outlined text-xl">history</span>
                                <span x-text="formatTime(timer)"></span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Submission Mode Toggle -->
                <div class="flex items-center gap-2 mb-4 bg-white/50 p-1.5 rounded-2xl border border-stone-200 w-fit">
                    <button 
                        @click="$wire.set('submission_type', 'text')" 
                        :disabled="isStarted || isSubmitted"
                        class="px-6 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all flex items-center gap-2"
                        :class="$wire.submission_type == 'text' ? 'bg-orange-800 text-white shadow-lg' : 'text-stone-500 hover:bg-stone-100'"
                    >
                        <span class="material-symbols-outlined text-sm">edit_note</span>
                        Typed Scholarly Response
                    </button>
                    <button 
                        @click="$wire.set('submission_type', 'file')" 
                        :disabled="isStarted || isSubmitted"
                        class="px-6 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all flex items-center gap-2"
                        :class="$wire.submission_type == 'file' ? 'bg-orange-800 text-white shadow-lg' : 'text-stone-500 hover:bg-stone-100'"
                    >
                        <span class="material-symbols-outlined text-sm">add_photo_alternate</span>
                        Handwritten / Attachment
                    </button>
                </div>

                <!-- Editor Area -->
                <div class="relative bg-white border border-stone-200 rounded-[2rem] shadow-xl flex flex-col min-h-[600px] overflow-hidden transition-all" :class="($wire.submission_type === 'text' && !isStarted && !isSubmitted) ? 'opacity-50 grayscale-[0.5]' : 'focus-within:ring-2 focus-within:ring-orange-800/20'">
                    <!-- Text Area / Markdown Editor -->
                    <div class="flex-1 relative">
                        @if($submission_type === 'file')
                            <div wire:key="file-submission-zone" class="p-12 flex flex-col items-center justify-center h-full min-h-[500px]">
                                @if($attachment_path)
                                    <div class="mb-8 w-full max-w-md">
                                        <div class="p-6 bg-stone-50 rounded-[2rem] border border-stone-200 flex items-center justify-between group">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-orange-800">
                                                        {{ str_ends_with($attachment_path, '.pdf') ? 'picture_as_pdf' : 'image' }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-stone-700">Attachment Ready</span>
                                                    <a href="{{ Storage::url($attachment_path) }}" target="_blank" class="text-[9px] text-orange-800 font-bold uppercase tracking-widest hover:underline flex items-center gap-1 mt-1">
                                                        Preview Submission <span class="material-symbols-outlined text-[10px]">open_in_new</span>
                                                    </a>
                                                </div>
                                            </div>
                                            @if(!$is_submitted)
                                                <button @click="$wire.set('attachment_path', null)" class="w-8 h-8 rounded-full hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition-colors text-stone-300">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if(!$is_submitted)
                                    <label class="w-full max-w-md border-2 border-dashed border-stone-200 rounded-[3rem] p-16 flex flex-col items-center justify-center cursor-pointer hover:border-orange-800/30 transition-all group bg-stone-50/30">
                                        <input type="file" wire:model="attachment" class="hidden" accept="image/*,application/pdf">
                                        
                                        <div class="w-20 h-20 bg-white rounded-3xl shadow-sm flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500">
                                            <span class="material-symbols-outlined text-4xl text-stone-300 group-hover:text-orange-800 transition-colors">
                                                {{ $attachment ? 'check_circle' : 'cloud_upload' }}
                                            </span>
                                        </div>
                                        
                                        <h5 class="text-lg font-bold text-stone-800 mb-1 font-headline italic">
                                            {{ $attachment ? 'File Selected' : 'Upload Handwritten Answer' }}
                                        </h5>
                                        <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest text-center max-w-[200px]">
                                            {{ $attachment ? 'Ready for upload' : 'Snap a photo of your work or upload a PDF document' }}
                                        </p>
                                        
                                        <div wire:loading wire:target="attachment" class="mt-8">
                                            <div class="flex items-center gap-3 px-4 py-2 bg-orange-800 rounded-full">
                                                <div class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                                <span class="text-[8px] text-white font-bold uppercase tracking-widest">Uploading to server...</span>
                                            </div>
                                        </div>

                                        @error('attachment')
                                            <div class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg text-[10px] font-bold uppercase tracking-widest border border-red-100">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </label>
                                @endif
                            </div>
                        @else
                            <div wire:key="text-submission-zone" class="h-full" wire:ignore>
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
                        @endif
                    </div>

                    <!-- Status Bar -->
                    @if($submission_type === 'text')
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
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    @if($submission_type === 'file' && !$is_submitted)
                        <button @click="executeSubmit()" class="px-10 py-3.5 bg-orange-800 text-white font-bold rounded-2xl hover:bg-orange-900 transition-all shadow-xl shadow-orange-900/20 text-xs uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">send</span>
                            Submit Final Attachment
                        </button>
                    @elseif($is_started && !$is_submitted)
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
                            class="w-full flex items-center justify-between p-8 rounded-[2.5rem] transition-all shadow-lg group overflow-hidden relative text-left"
                            :class="isSubmitted ? 'bg-white border-2 border-stone-900/5 text-stone-900 hover:border-orange-800/20' : 'bg-white border border-stone-200 hover:border-orange-800/30'"
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
                <section class="mt-8" x-data="{ activeTab: 'expert' }">
                    <div class="flex border-b border-stone-200 mb-8">
                        <button 
                            @click="activeTab = 'expert'"
                            class="px-8 py-4 font-bold text-xs uppercase tracking-widest transition-all border-b-2"
                            :class="activeTab === 'expert' ? 'border-orange-800 text-orange-800' : 'border-transparent text-stone-400 hover:text-stone-600'"
                        >Expert Evaluation</button>
                        <button 
                            @click="activeTab = 'peer'"
                            class="px-8 py-4 font-bold text-xs uppercase tracking-widest transition-all border-b-2"
                            :class="activeTab === 'peer' ? 'border-orange-800 text-orange-800' : 'border-transparent text-stone-400 hover:text-stone-600'"
                        >
                            Peer Evaluation 
                            <span class="ml-2 bg-stone-100 text-stone-600 px-2 py-0.5 rounded-full text-[8px]">{{ $comments->count() }}</span>
                        </button>
                    </div>

                    <div x-show="activeTab === 'expert'">
                        <div class="flex flex-col gap-4">
                            @if($is_submitted && $submission && $submission->evaluated_at)
                                <div class="p-8 bg-white rounded-[2rem] border border-stone-200 relative overflow-hidden shadow-sm">
                                    <div class="grid grid-cols-2 gap-4 mb-8">
                                        <div class="bg-stone-50 p-4 rounded-2xl border border-stone-100">
                                            <span class="text-[8px] font-bold uppercase tracking-widest text-stone-400 block mb-1">Scholar Score</span>
                                            <div class="flex items-end gap-1">
                                                <span class="text-2xl font-bold text-stone-900 leading-none">{{ $submission->score }}</span>
                                                <span class="text-xs font-bold text-stone-400 mb-0.5">/ {{ $question->marks ?: 100 }}</span>
                                            </div>
                                        </div>
                                        <div class="bg-stone-50 p-4 rounded-2xl border border-stone-100">
                                            <span class="text-[8px] font-bold uppercase tracking-widest text-stone-400 block mb-1">Evaluation Date</span>
                                            <span class="text-xs font-bold text-stone-900 leading-none">{{ $submission->evaluated_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>

                                    @if($submission->evaluation_attachment_path)
                                        <div class="mb-8 p-5 bg-orange-800/5 rounded-[2rem] border border-orange-800/10 flex items-center justify-between group">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-orange-800">
                                                        {{ str_ends_with($submission->evaluation_attachment_path, '.pdf') ? 'picture_as_pdf' : 'description' }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-stone-700 leading-none mb-1">Evaluation Document</span>
                                                    <span class="text-[9px] text-stone-400 font-bold uppercase tracking-widest">Marked-up Response Attached</span>
                                                </div>
                                            </div>
                                            <a href="{{ Storage::url($submission->evaluation_attachment_path) }}" download class="px-6 py-2.5 bg-orange-800 text-white rounded-xl text-[9px] font-bold uppercase tracking-widest hover:bg-orange-900 transition-all shadow-lg shadow-orange-900/20 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-sm">download</span>
                                                Download
                                            </a>
                                        </div>
                                    @endif

                                    <div class="prose prose-stone max-w-none prose-p:text-sm prose-p:leading-relaxed prose-p:text-stone-600 italic">
                                        {!! Str::markdown($submission->feedback_text ?? '') !!}
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
                    </div>

                    <div x-show="activeTab === 'peer'" x-cloak>
                        <div class="flex flex-col gap-6">
                            <!-- Comment Form -->
                            @auth
                                <div class="bg-white border border-stone-200 rounded-[2rem] p-8 shadow-sm">
                                    <h4 class="text-sm font-bold text-stone-900 font-headline italic mb-4">Post Peer Evaluation</h4>
                                    <textarea 
                                        wire:model="comment_content"
                                        class="w-full bg-stone-50 border-stone-100 rounded-2xl p-6 text-sm text-stone-600 focus:ring-orange-800 focus:border-orange-800 min-h-[120px] mb-4"
                                        placeholder="Share your anthropological insights or structure recommendations..."
                                    ></textarea>
                                    <div class="flex justify-end">
                                        <button 
                                            wire:click="addComment"
                                            class="px-8 py-3 bg-stone-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-orange-800 transition-colors flex items-center gap-2"
                                        >
                                            <span class="material-symbols-outlined text-sm">post_add</span>
                                            Submit Evaluation
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="bg-stone-50 border border-dashed border-stone-200 rounded-[2rem] p-8 text-center">
                                    <p class="text-sm text-stone-500 mb-4">You must be logged in to participate in peer evaluations.</p>
                                    <a wire:navigate href="{{ route('login') }}" class="inline-block px-6 py-2 bg-stone-900 text-white rounded-lg text-[10px] font-bold uppercase tracking-widest">Log In Now</a>
                                </div>
                            @endauth

                            <!-- Comments List -->
                            <div class="space-y-6">
                                @forelse($comments as $comment)
                                    <div class="bg-white border border-stone-200 rounded-[2rem] p-8 shadow-sm">
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-sandstone rounded-xl flex items-center justify-center text-charcoal font-bold text-xs shadow-sm">
                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h5 class="text-sm font-bold text-stone-900">{{ $comment->user->name }}</h5>
                                                    <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest">{{ $comment->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 text-orange-800">
                                                <span class="material-symbols-outlined text-sm">verified_user</span>
                                                <span class="text-[8px] font-black uppercase tracking-widest">Scholar</span>
                                            </div>
                                        </div>
                                        <div class="prose prose-stone prose-sm max-w-none text-stone-600 leading-relaxed">
                                            {{ $comment->content }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <span class="material-symbols-outlined text-4xl text-stone-200 mb-2">forum</span>
                                        <p class="text-stone-400 italic text-sm">No peer evaluations yet. Be the first to start the scholarly discussion!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
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
                        @foreach($allSubmissions as $historySub)
                            <button 
                                wire:click="selectSubmission({{ $historySub->id }})" 
                                class="w-full flex items-center justify-between p-4 rounded-2xl border transition-all text-left"
                                :class="$wire.active_submission_id == {{ $historySub->id }} ? 'bg-orange-800 border-orange-800 text-white shadow-lg' : 'bg-stone-50 border-stone-100 text-stone-600 hover:border-orange-800/30'"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-stone-50 flex items-center justify-center transition-colors group-hover:bg-white">
                                        <span class="material-symbols-outlined text-stone-400 group-hover:text-orange-800">
                                            {{ $historySub->submission_type === 'file' ? 'attachment' : 'history_edu' }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-stone-800">Attempt #{{ $historySub->attempts_count }}</span>
                                        <span class="text-[8px] text-stone-400 uppercase font-bold tracking-widest">{{ $historySub->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($historySub->status === 'submitted')
                                        @if($historySub->evaluated_at)
                                            <span class="text-xs font-black">{{ $historySub->score }}/{{ $question->marks ?: 100 }}</span>
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
                    <div class="bg-white border border-stone-200 rounded-[2.5rem] p-8 text-stone-900 shadow-sm">
                        <h4 class="text-lg font-bold text-orange-800 font-headline italic mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined">analytics</span>
                            Scoring Criteria
                        </h4>
                        <div class="space-y-4">
                            @foreach($question->evaluation_rubric as $rubric)
                                <div class="flex justify-between items-start gap-4 pb-3 border-b border-stone-50 last:border-0">
                                    <span class="text-xs text-stone-600 leading-relaxed">{{ $rubric['criteria'] }}</span>
                                    <span class="text-xs font-bold text-orange-800 whitespace-nowrap">{{ $rubric['marks'] }}M</span>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-6 text-[8px] uppercase tracking-[0.2em] text-stone-400 font-bold">Standard UPSC Marking Scheme applied</p>
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

        <!-- Model Answer Restriction Modal -->
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
                    <span class="material-symbols-outlined text-4xl">lock</span>
                </div>
                <h2 class="text-3xl font-headline italic font-bold text-stone-900 mb-4">Attempt Required</h2>
                <p class="text-stone-600 mb-10 leading-relaxed text-sm">
                    Anthropological mastery is built through active recall. To unlock the <span class="font-bold text-stone-900 italic">expert model answer</span>, you must first complete and submit your own attempt.
                </p>
                <div class="flex flex-col gap-3">
                    <button @click="showModelAnswerConfirm = false" class="w-full py-4 bg-stone-900 text-white font-bold rounded-2xl shadow-xl hover:-translate-y-1 transition-all uppercase tracking-widest text-[10px]">
                        I'll Practice Now
                    </button>
                    <p class="text-[9px] text-stone-400 font-bold uppercase tracking-widest">Submit your scholarly response to unlock</p>
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

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-12 no-scrollbar">
                    <div class="max-w-3xl mx-auto">
                        <!-- The Answer (In Modal) -->
                        <div class="prose prose-stone prose-lg max-w-none prose-headings:font-headline prose-headings:italic prose-blockquote:border-orange-800">
                            @if($question->model_answer)
                                {!! Str::markdown($question->model_answer) !!}
                            @else
                                <div class="py-20 text-center text-stone-400 italic">
                                    Model answer content is currently being curated by our senior faculty. Check back soon.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dedicated Printable Container (Always in DOM, Hidden from Screen) -->
        
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
        @endif
    </main>

    <!-- Dedicated Printable Container (Always in DOM, Hidden from Screen) -->
    <div id="printable-model-answer" class="hidden">
        <div class="max-w-3xl mx-auto">
            <!-- Branding for Print -->
            <div class="mb-10 border-b-2 border-stone-900 pb-6">
                <h1 class="text-3xl font-headline italic font-bold text-stone-900">AnthroConnect Archivist</h1>
                <p class="text-[10px] font-bold text-stone-500 uppercase tracking-widest mt-2">Professional Answer Writing Practice • Model Response</p>
            </div>

            <!-- Question Context -->
            <div class="mb-10 p-8 bg-stone-50 rounded-3xl border border-stone-100">
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



            <!-- Footer for Print -->
            <div class="mt-12 pt-6 border-t border-stone-100 text-[8px] text-stone-400 font-bold uppercase tracking-[0.2em] text-center">
                © {{ date('Y') }} AnthroConnect Archivist Portal • Scholarly Resource for UPSC Anthropology
            </div>
        </div>
    </div>
</div>
