<div class="space-y-8">
    <!-- Header with Stats -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-surface-container-low/30 p-8 rounded-[32px] border border-outline-variant/10">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-[24px] bg-primary/10 text-primary flex items-center justify-center shadow-inner">
                <span class="material-symbols-outlined text-3xl">psychology</span>
            </div>
            <div>
                <h3 class="font-headline text-3xl text-on-surface italic font-bold">Class MCQ Assessment</h3>
                <p class="text-xs text-stone-500 font-medium tracking-tight mt-1">Manage assessment parameters and challenge questions</p>
            </div>
            <div class="h-10 w-px bg-stone-200"></div>
            <div class="flex items-center gap-6">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-primary">Total Marks</span>
                    <span class="text-sm font-bold text-stone-900">{{ $stats['total_marks'] }} {{ $stats['total_marks'] == 1 ? 'Mark' : 'Marks' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            @if($view === 'questions')
                <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl border border-outline-variant/10 shadow-sm">
                    <div class="flex flex-col items-end">
                        <span class="text-[9px] font-bold uppercase tracking-widest {{ $is_assessment_published ? 'text-primary' : 'text-stone-400' }}">
                            Assessment {{ $is_assessment_published ? 'Live' : 'Hidden' }}
                        </span>
                        <span class="text-[8px] text-stone-400 italic">Global visibility</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:click="toggleAssessmentPublish" class="sr-only peer" {{ $is_assessment_published ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>

                <button wire:click="openCreateModal" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 transition-all flex items-center gap-2 group">
                    <span class="material-symbols-outlined text-sm group-hover:rotate-90 transition-transform">add</span>
                    Add Question
                </button>
            @endif
        </div>
    </div>

    <!-- View Switcher -->
    <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-2xl w-fit">
        <button wire:click="$set('view', 'questions')" class="px-8 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all {{ $view === 'questions' ? 'bg-white text-primary shadow-sm' : 'text-stone-400 hover:text-stone-600' }}">
            Question Bank
        </button>
        <button wire:click="$set('view', 'results')" class="px-8 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all {{ $view === 'results' ? 'bg-white text-primary shadow-sm' : 'text-stone-400 hover:text-stone-600' }}">
            Student Submissions
        </button>
    </div>

    @if($view === 'questions')
        <!-- Toolbar & Configuration -->
        <div class="flex items-center justify-between gap-4">
            <div class="bg-surface-container-low/50 rounded-2xl p-4 flex flex-wrap items-center gap-4 border border-outline-variant/10 flex-1">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search questions..." 
                        class="w-full pl-9 pr-4 py-2 bg-white border border-outline-variant/20 rounded-xl text-xs font-medium focus:ring-1 focus:ring-primary outline-none">
                </div>
            </div>

            <button wire:click="openConfigModal" class="bg-stone-900 text-white px-8 py-4 rounded-2xl font-bold text-[10px] uppercase tracking-widest shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-3 shrink-0">
                <span class="material-symbols-outlined text-sm">settings</span>
                Configure Assessment
            </button>
        </div>

        @if($assessment)
            <!-- Questions Table -->
            <div class="bg-white rounded-[32px] border border-outline-variant/10 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/30 border-b border-outline-variant/10">
                            <th class="w-16 p-6"></th>
                            <th class="w-12 p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400">S/N</th>
                            <th class="p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400">Question Inquiry</th>
                            <th class="p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400">Correct Response</th>
                            <th class="p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400 text-center">Marks</th>
                            <th class="w-20 p-6"></th>
                        </tr>
                    </thead>
                    <tbody x-data 
                           x-init="new Sortable($el, { 
                               handle: '.drag-handle', 
                               ghostClass: 'sortable-ghost',
                               animation: 150,
                               onEnd: (evt) => {
                                   let items = Array.from($el.querySelectorAll('[data-id]')).map(el => el.getAttribute('data-id'));
                                   $wire.updateOrder(items);
                               }
                           })">
                        @forelse($questions as $question)
                            <tr wire:key="mcq-row-{{ $question->id }}" 
                                data-id="{{ $question->id }}"
                                class="group border-b border-stone-50 hover:bg-stone-50/50 transition-colors">
                                
                                <td class="p-6">
                                    <div class="drag-handle cursor-move text-stone-300 hover:text-primary transition-colors flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[20px]">drag_indicator</span>
                                    </div>
                                </td>

                                <td class="p-6">
                                    <span class="text-[10px] font-bold text-stone-400 font-mono">#{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>

                                <td class="p-6 max-w-md">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-stone-900 italic line-clamp-1 group-hover:text-primary transition-colors">
                                            {{ $question->question_text }}
                                        </span>
                                        @if($question->explanation)
                                            <span class="text-[10px] text-stone-400 italic mt-1 line-clamp-1">
                                                {{ $question->explanation }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-6">
                                    @php $correctOption = $question->options->where('is_correct', true)->first(); @endphp
                                    @if($correctOption)
                                        <div class="flex items-center gap-2">
                                            <div class="w-5 h-5 rounded-full bg-green-500/10 text-green-600 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[12px] font-bold">check</span>
                                            </div>
                                            <span class="text-[11px] font-bold text-stone-600 italic truncate max-w-[200px]">
                                                {{ $correctOption->option_text }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-error font-bold uppercase italic">No Correct Answer Set</span>
                                    @endif
                                </td>

                                <td class="p-6 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full bg-primary/5 text-primary text-[10px] font-bold uppercase tracking-widest">
                                        {{ $question->marks }} {{ $question->marks == 1 ? 'Mark' : 'Marks' }}
                                    </span>
                                </td>

                                <td class="p-6">
                                    <div class="flex justify-end" x-data="{ open: false }">
                                        <div class="relative">
                                            <button @click="open = !open" class="w-8 h-8 rounded-lg flex items-center justify-center text-stone-400 hover:bg-stone-100 hover:text-stone-900 transition-all">
                                                <span class="material-symbols-outlined text-xl">more_vert</span>
                                            </button>
                                            
                                            <div x-show="open" 
                                                 @click.away="open = false" 
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-outline-variant/10 z-30 overflow-hidden py-2" 
                                                 x-cloak>
                                                
                                                <button wire:click="openEditModal({{ $question->id }})" @click="open = false" class="w-full px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-stone-600 hover:bg-stone-50 flex items-center gap-3">
                                                    <span class="material-symbols-outlined text-sm">edit</span>
                                                    Refine Record
                                                </button>
                                                
                                                <button wire:click="duplicate({{ $question->id }})" @click="open = false" class="w-full px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-stone-600 hover:bg-stone-50 flex items-center gap-3">
                                                    <span class="material-symbols-outlined text-sm">content_copy</span>
                                                    Duplicate Assessment
                                                </button>
                                                
                                                <div class="h-px bg-stone-100 my-1"></div>
                                                
                                                <button wire:click="delete({{ $question->id }})" 
                                                        wire:confirm="Are you sure you want to delete this MCQ? This action is permanent."
                                                        @click="open = false" 
                                                        class="w-full px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-error hover:bg-error/5 flex items-center gap-3">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                    Purge Assessment
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center mb-6">
                                            <span class="material-symbols-outlined text-3xl text-stone-300">quiz</span>
                                        </div>
                                        <h4 class="font-headline text-2xl font-bold italic text-stone-900">No questions added yet</h4>
                                        <p class="text-sm text-stone-500 max-w-sm mt-2 italic">Add questions to this assessment challenge.</p>
                                        <button wire:click="openCreateModal" class="mt-8 text-primary font-bold uppercase tracking-widest text-[10px] hover:underline">Add First Question</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-[32px] border border-outline-variant/10 shadow-sm p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-primary/5 text-primary flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">assignment_add</span>
                </div>
                <h4 class="font-headline text-3xl font-bold italic text-stone-900">Initialize Assessment</h4>
                <p class="text-stone-500 max-w-md mx-auto mt-4 text-sm font-medium leading-relaxed italic">
                    This class does not yet have an active assessment protocol. Configure the test parameters to begin adding questions.
                </p>
                <button wire:click="openConfigModal" class="mt-10 bg-primary text-on-primary px-10 py-4 rounded-2xl font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 transition-all">
                    Configure Test Parameters
                </button>
            </div>
        @endif
    @else
        <!-- Results View Toolbar -->
        <div class="flex items-center justify-between gap-4">
            <div class="flex-1">
                <h4 class="font-headline text-xl font-bold italic text-stone-900">Scholars' Performance Record</h4>
                <p class="text-[10px] text-stone-500 font-medium tracking-tight mt-1 uppercase">Comprehensive attempt log and mastery audit</p>
            </div>

            <button wire:click="exportCsv" class="bg-stone-900 text-white px-8 py-3.5 rounded-2xl font-bold text-[10px] uppercase tracking-widest shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-3 shrink-0 group">
                <span class="material-symbols-outlined text-sm group-hover:translate-y-0.5 transition-transform">download</span>
                Export Scholars CSV
            </button>
        </div>

        <!-- Results View Table -->
        <div class="bg-white rounded-[32px] border border-outline-variant/10 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/30 border-b border-outline-variant/10">
                        <th class="p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400">Scholar</th>
                        <th class="p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400">Date Attempted</th>
                        <th class="p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400 text-center">Score</th>
                        <th class="p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400 text-center">Percentage</th>
                        <th class="p-6 text-[10px] font-bold uppercase tracking-widest text-stone-400">Mastery Status</th>
                        <th class="w-20 p-6"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                        <tr class="group border-b border-stone-50 hover:bg-stone-50/50 transition-colors">
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-stone-100 flex items-center justify-center text-stone-400 overflow-hidden border border-stone-200">
                                        @if($attempt->user->profile_photo_url)
                                            <img src="{{ $attempt->user->profile_photo_url }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-xl">person</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-stone-900 italic">{{ $attempt->user->name }}</span>
                                        <span class="text-[9px] text-stone-400 uppercase font-bold tracking-widest">{{ $attempt->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <div class="flex flex-col">
                                    <span class="text-xs font-medium text-stone-600">{{ $attempt->submitted_at->format('M d, Y') }}</span>
                                    <span class="text-[9px] text-stone-400 italic">{{ $attempt->submitted_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="p-6 text-center">
                                <span class="text-sm font-bold text-stone-900">{{ $attempt->score }} <span class="text-stone-300 mx-1">/</span> {{ $attempt->total_marks }}</span>
                            </td>
                            <td class="p-6 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-xs font-black {{ $attempt->passed ? 'text-green-600' : 'text-error' }}">
                                        {{ number_format($attempt->percentage, 1) }}%
                                    </span>
                                    <div class="w-16 h-1 bg-stone-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $attempt->passed ? 'bg-green-500' : 'bg-error' }}" style="width: {{ $attempt->percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full {{ $attempt->passed ? 'bg-green-500' : 'bg-error' }}"></div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest {{ $attempt->passed ? 'text-green-700' : 'text-error' }}">
                                        {{ $attempt->summary }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-6 text-right">
                                <button wire:click="deleteAttempt({{ $attempt->id }})" 
                                        wire:confirm="Are you sure you want to delete this attempt record?"
                                        class="text-stone-300 hover:text-error transition-colors">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center mb-6">
                                        <span class="material-symbols-outlined text-3xl text-stone-300">group_off</span>
                                    </div>
                                    <h4 class="font-headline text-2xl font-bold italic text-stone-900">No submissions yet</h4>
                                    <p class="text-sm text-stone-500 max-w-sm mt-2 italic">Scholars have not yet attempted this assessment protocol.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Assessment Config Modal -->
    <div x-show="$wire.isConfigModalOpen" 
         class="fixed inset-0 z-[110] flex items-center justify-center p-6" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);" class="fixed inset-0" @click="$wire.isConfigModalOpen = false"></div>
        
        <div class="bg-surface-container-lowest rounded-[32px] shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col relative z-10 animate-in zoom-in-95 duration-200 overflow-hidden">
            <!-- Header -->
            <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                <div>
                    <h4 class="font-headline text-2xl text-on-surface italic font-bold">Configure Assessment</h4>
                    <p class="text-[10px] uppercase font-bold tracking-widest text-stone-400 mt-1">LMS Test Parameters & Logic</p>
                </div>
                <button @click="$wire.isConfigModalOpen = false" class="text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] uppercase font-bold text-stone-500 tracking-widest px-1">Assessment Title</label>
                            <input wire:model="assessment_title" type="text" placeholder="Enter test title..." class="w-full bg-stone-50 border border-outline-variant/30 rounded-2xl p-5 text-lg font-bold italic focus:ring-2 focus:ring-primary outline-none transition-all">
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] uppercase font-bold text-stone-500 tracking-widest px-1">Detailed Description</label>
                            <textarea wire:model="assessment_description" rows="3" placeholder="Brief context for the scholars..." class="w-full bg-stone-50 border border-outline-variant/30 rounded-2xl p-5 text-sm font-medium focus:ring-2 focus:ring-primary outline-none transition-all"></textarea>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] uppercase font-bold text-stone-500 tracking-widest px-1">Instructions for Scholars</label>
                            <textarea wire:model="assessment_instructions" rows="4" placeholder="Guidance on how to approach this test..." class="w-full bg-stone-50 border border-outline-variant/30 rounded-2xl p-5 text-sm font-medium focus:ring-2 focus:ring-primary outline-none transition-all"></textarea>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-stone-50 rounded-[24px] border border-stone-100 p-6 space-y-6">
                            <h5 class="text-[10px] uppercase font-bold text-stone-400 tracking-widest border-b border-stone-100 pb-3">Logic & Rules</h5>
                            
                            <div class="space-y-5">
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-stone-800 italic">Duration</span>
                                        <span class="text-[8px] text-stone-400 uppercase font-bold">Minutes</span>
                                    </div>
                                    <input wire:model="duration_minutes" type="number" class="w-16 bg-white border border-stone-200 rounded-lg px-2 py-1.5 text-right font-bold text-xs">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-stone-800 italic">Passing Marks</span>
                                        <span class="text-[8px] text-stone-400 uppercase font-bold">Min. Percentage</span>
                                    </div>
                                    <input wire:model="passing_marks" type="number" class="w-16 bg-white border border-stone-200 rounded-lg px-2 py-1.5 text-right font-bold text-xs">
                                </div>

                                <div class="h-px bg-stone-100"></div>

                                <label class="flex items-center justify-between cursor-pointer group py-1">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-stone-700 italic group-hover:text-primary transition-colors">Allow Retakes</span>
                                        <span class="text-[8px] text-stone-400 font-bold uppercase tracking-tighter">Unlimited attempts</span>
                                    </div>
                                    <div class="relative inline-flex items-center">
                                        <input wire:model="allow_retake" type="checkbox" class="sr-only peer">
                                        <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary shadow-sm"></div>
                                    </div>
                                </label>

                                <label class="flex items-center justify-between cursor-pointer group py-1">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-stone-700 italic group-hover:text-primary transition-colors">Show Results</span>
                                        <span class="text-[8px] text-stone-400 font-bold uppercase tracking-tighter">Score after submit</span>
                                    </div>
                                    <div class="relative inline-flex items-center">
                                        <input wire:model="show_results_immediately" type="checkbox" class="sr-only peer">
                                        <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary shadow-sm"></div>
                                    </div>
                                </label>

                                <label class="flex items-center justify-between cursor-pointer group py-1">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-stone-700 italic group-hover:text-primary transition-colors">Shuffle Order</span>
                                        <span class="text-[8px] text-stone-400 font-bold uppercase tracking-tighter">Randomize questions</span>
                                    </div>
                                    <div class="relative inline-flex items-center">
                                        <input wire:model="randomize_questions" type="checkbox" class="sr-only peer">
                                        <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary shadow-sm"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-8 border-t border-outline-variant/10 flex justify-end gap-4 bg-surface-container-low/30">
                <button @click="$wire.isConfigModalOpen = false" class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 rounded-xl transition-all">Discard</button>
                <button wire:click="saveAssessment" class="bg-stone-900 text-white px-10 py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Commit Parameters
                </button>
            </div>
        </div>
    </div>

    <!-- Question Modal -->
    <div x-show="$wire.isModalOpen" 
         class="fixed inset-0 z-[110] flex items-center justify-center p-6" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);" class="fixed inset-0" @click="$wire.isModalOpen = false"></div>
        
        <div class="bg-surface-container-lowest rounded-[32px] shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col relative z-10 animate-in zoom-in-95 duration-200 overflow-hidden">
            <!-- Header -->
            <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                <div>
                    <h4 class="font-headline text-2xl text-on-surface italic font-bold">
                        {{ $editingQuestionId ? 'Refine Question' : 'Deposit New Question' }}
                    </h4>
                    <p class="text-[10px] uppercase font-bold tracking-widest text-stone-400 mt-1">LMS Class Challenge Protocol</p>
                </div>
                <button @click="$wire.isModalOpen = false" class="text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                    <div class="md:col-span-8 space-y-8">
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-bold text-stone-500 tracking-widest px-1">Question Inquiry</label>
                            <textarea wire:model="question_text" rows="4" placeholder="Enter the question text..." class="w-full bg-surface-container-low border border-outline-variant/30 rounded-[24px] p-6 text-base font-bold italic focus:ring-2 focus:ring-primary outline-none transition-all"></textarea>
                            @error('question_text') <span class="text-error text-[10px] font-bold uppercase px-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center justify-between px-1">
                                <label class="text-[10px] uppercase font-bold text-stone-500 tracking-widest">Challenge Options</label>
                                <button wire:click="addOption" class="text-primary text-[10px] font-bold uppercase tracking-widest hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">add_circle</span> Add Choice
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                @foreach($options as $index => $option)
                                    <div class="flex items-center gap-4 group">
                                        <button wire:click="setCorrectOption({{ $index }})" 
                                                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-all border {{ $correct_option_index == $index ? 'bg-green-500 text-white border-green-600 shadow-lg shadow-green-200' : 'bg-stone-50 text-stone-300 border-stone-200 hover:border-stone-400' }}">
                                            <span class="material-symbols-outlined text-xl">{{ $correct_option_index == $index ? 'check_circle' : 'radio_button_unchecked' }}</span>
                                        </button>
                                        
                                        <div class="flex-1 relative">
                                            <input wire:model="options.{{ $index }}.text" type="text" placeholder="Enter option text..." class="w-full bg-stone-50 border {{ $correct_option_index == $index ? 'border-green-200 bg-green-50/30' : 'border-stone-100' }} rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary outline-none transition-all">
                                            @error('options.'.$index.'.text') <span class="text-error text-[8px] font-bold absolute -bottom-4 left-0">{{ $message }}</span> @enderror
                                        </div>

                                        @if(count($options) > 2)
                                            <button wire:click="removeOption({{ $index }})" class="w-10 h-10 rounded-xl bg-stone-50 text-stone-300 hover:bg-error/10 hover:text-error transition-all flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-bold text-stone-500 tracking-widest px-1">Explanation (Visible after answer)</label>
                            <textarea wire:model="explanation" rows="3" placeholder="Contextual explanation for the correct choice..." class="w-full bg-surface-container-low border border-outline-variant/30 rounded-[20px] p-5 text-sm font-medium italic focus:ring-2 focus:ring-primary outline-none transition-all"></textarea>
                        </div>
                    </div>

                    <div class="md:col-span-4 space-y-8">
                        <div class="space-y-3">
                            <label class="text-[10px] uppercase font-bold text-stone-500 tracking-widest px-1">Mark Value</label>
                            <input wire:model="marks" type="number" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-8 border-t border-outline-variant/10 flex justify-end gap-4 bg-surface-container-low/30">
                <button @click="$wire.isModalOpen = false" class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 rounded-xl transition-all">Discard</button>
                <button wire:click="save" class="bg-primary text-on-primary px-10 py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 transition-all">
                    {{ $editingQuestionId ? 'Update Record' : 'Deposit Assessment' }}
                </button>
            </div>
        </div>
    </div>
</div>
