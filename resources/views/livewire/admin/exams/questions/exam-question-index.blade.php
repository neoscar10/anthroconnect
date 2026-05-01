<div x-data="{ isModalOpen: @entangle('isModalOpen'), activeTab: @entangle('activeTab') }" 
     @open-modal.window="isModalOpen = true; document.body.style.overflow = 'hidden'" 
     @close-modal.window="isModalOpen = false; document.body.style.overflow = 'auto'" 
     class="relative p-6 lg:p-10">
    
    @if(session('success'))
        <div class="mb-8 px-6 py-4 bg-primary/10 text-primary rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-primary/20 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                {{ session('success') }}
            </div>
            <button onclick="this.parentElement.remove()" class="hover:rotate-180 transition-transform duration-500">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-12 gap-6">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Exam Questions</h1>
            <p class="font-body text-on-surface-variant text-lg">Manage answer-writing practice questions, guidelines, tags, and model answers.</p>
        </div>
        <div class="flex gap-4">
            <button type="button" wire:click="openCreateModal" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm">add_circle</span>
                Add Question
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-12">
        <div class="bg-surface-container-lowest p-6 rounded-[32px] border border-outline-variant/10 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 bg-stone-100 rounded-2xl flex items-center justify-center text-stone-500">
                <span class="material-symbols-outlined">quiz</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-on-surface">{{ $stats['total'] }}</p>
                <p class="text-[10px] uppercase font-bold text-stone-400 tracking-widest">Total Questions</p>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-[32px] border border-outline-variant/10 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600">
                <span class="material-symbols-outlined">verified</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-on-surface">{{ $stats['published'] }}</p>
                <p class="text-[10px] uppercase font-bold text-stone-400 tracking-widest">Published</p>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-[32px] border border-outline-variant/10 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600">
                <span class="material-symbols-outlined">edit_note</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-on-surface">{{ $stats['drafts'] }}</p>
                <p class="text-[10px] uppercase font-bold text-stone-400 tracking-widest">Drafts</p>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-[32px] border border-outline-variant/10 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined">edit_square</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-on-surface">{{ $stats['model'] }}</p>
                <p class="text-[10px] uppercase font-bold text-stone-400 tracking-widest">Model Questions</p>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-[32px] border border-outline-variant/10 shadow-sm flex items-center gap-4">
            <div class="h-12 w-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600">
                <span class="material-symbols-outlined">archive</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-on-surface">{{ $stats['past'] }}</p>
                <p class="text-[10px] uppercase font-bold text-stone-400 tracking-widest">Past Questions</p>
            </div>
        </div>
    </div>

    <!-- Management Controls -->
    <div class="bg-surface-container-lowest rounded-[32px] shadow-sm border border-outline-variant/10 overflow-hidden mb-8">
        <div class="p-6 border-b border-outline-variant/10 flex flex-wrap gap-4 items-center justify-between bg-surface-container-low/30">
            <div class="flex gap-4 items-center flex-wrap">
                <div class="relative flex-1 min-w-[250px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-outline-variant/20 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm" placeholder="Search questions..." type="text"/>
                </div>

                <select wire:model.live="status" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>

                <select wire:model.live="filters.question_kind" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Question Types</option>
                    <option value="model">Model Questions</option>
                    <option value="past">Past Questions</option>
                </select>

                <select wire:model.live="year" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Years</option>
                    @foreach(range(date('Y'), 2010) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>

                <div class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs font-bold text-on-surface shadow-sm">
                    UPSC Only
                </div>

                @foreach($filterableTagGroups as $group)
                    <select wire:model.live="tagFilters.{{ $group->id }}" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                        <option value="">All {{ $group->name }}</option>
                        @foreach($group->activeTags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto pb-24">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low/50">
                    <tr>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[9px] font-bold">Question Preview</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[9px] font-bold">Metadata</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[9px] font-bold text-center">Type</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[9px] font-bold text-center">Marks/Words</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[9px] font-bold">Status</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[9px] font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($questions as $question)
                        <tr class="hover:bg-surface-container-low/20 transition-colors group">
                            <td class="px-6 py-4 max-w-md">
                                <div class="flex flex-col">
                                    <p class="font-headline font-bold text-on-surface mb-1 leading-tight line-clamp-2">{{ $question->title ?: Str::limit($question->question_text, 80) }}</p>
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach($question->tags->take(3) as $tag)
                                            <span class="px-2 py-0.5 rounded bg-surface-container-high text-[8px] font-bold text-on-surface-variant">{{ $tag->name }}</span>
                                        @endforeach
                                        @if($question->tags->count() > 3)
                                            <span class="px-2 py-0.5 rounded bg-stone-100 text-[8px] font-bold text-stone-400">+{{ $question->tags->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                    <p class="text-xs font-bold text-on-surface">UPSC</p>
                                    <p class="text-[10px] text-stone-400">{{ $question->year ?: 'No Year' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($question->question_kind === 'past')
                                    <span class="badge bg-warning/10 text-warning text-[8px] font-bold uppercase tracking-widest px-2 py-1 rounded-lg">
                                        <i class="ri-archive-line align-middle me-1"></i> Past
                                    </span>
                                @else
                                    <span class="badge bg-primary/10 text-primary text-[8px] font-bold uppercase tracking-widest px-2 py-1 rounded-lg">
                                        <i class="ri-edit-2-line align-middle me-1"></i> Model
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-stone-700">{{ $question->marks }}M</span>
                                    <span class="text-[9px] text-stone-400 font-mono">{{ $question->word_limit }} words</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest {{ $question->status === 'published' ? 'bg-success/10 text-success' : ($question->status === 'draft' ? 'bg-stone-100 text-stone-500' : 'bg-warning/10 text-warning') }}">
                                        {{ $question->status }}
                                    </span>
                                    @if($question->access_type === 'member_only')
                                        <span class="flex items-center gap-1 text-[9px] font-bold text-secondary uppercase tracking-widest">
                                            <span class="material-symbols-outlined text-[12px]">lock</span> Members Only
                                        </span>
                                    @endif
                                </div>
                            </td>
                             <td class="px-6 py-4 text-right">
                                 <div x-data="{ open: false }" class="relative inline-flex justify-end items-center">
                                     <button @click="open = !open" @click.away="open = false" class="p-2 text-stone-400 hover:text-on-surface transition-colors rounded-full hover:bg-surface-container-high">
                                         <span class="material-symbols-outlined text-sm">more_vert</span>
                                     </button>
                                     
                                     <div x-show="open" x-cloak
                                          x-transition:enter="transition ease-out duration-200"
                                          x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                                          x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                          class="absolute right-0 top-1/2 -translate-y-1/2 mr-10 w-48 bg-surface-container-lowest rounded-xl shadow-2xl border border-outline-variant/20 z-[100] overflow-hidden">
                                         <button type="button" wire:click="openEditModal({{ $question->id }})" @click="open = false" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                             <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                                             Edit Question
                                         </button>
                                         <button type="button" wire:click="toggleStatus({{ $question->id }})" @click="open = false" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                             <span class="material-symbols-outlined text-sm">{{ $question->status == 'published' ? 'archive' : 'publish' }}</span>
                                             {{ $question->status == 'published' ? 'Archive' : 'Publish' }}
                                         </button>
                                         <button type="button" wire:click="duplicate({{ $question->id }})" @click="open = false" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-all flex items-center gap-2 border-t border-outline-variant/10">
                                             <span class="material-symbols-outlined text-sm text-blue-500">content_copy</span>
                                             Duplicate
                                         </button>
                                         <button type="button" @click="open = false; $dispatch('open-delete-modal', { 
                                                     title: 'Delete Question', 
                                                     message: 'Are you sure you want to delete this question? This action will permanently remove it from the archive.', 
                                                     action: { type: 'livewire', component: '{{ $this->getId() }}', method: 'delete', params: [{{ $question->id }}] } 
                                                 })" 
                                                 class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-error hover:bg-error/5 transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                             <span class="material-symbols-outlined text-sm">delete</span>
                                             Delete
                                         </button>
                                     </div>
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-6xl text-outline-variant/30 mb-4">quiz</span>
                                    <p class="text-on-surface-variant font-headline text-2xl italic font-bold">No Questions Yet.</p>
                                    <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-2 max-w-xs leading-relaxed">Start by adding your first answer-writing practice question to the UPSC database.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($questions->hasPages())
            <div class="px-6 py-6 border-t border-outline-variant/10 bg-surface-container-low/10">
                {{ $questions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Layout -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
        <div x-show="isModalOpen" 
             style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);"
             class="fixed inset-0 transition-opacity"
             @click="isModalOpen = false; $wire.resetForm()"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"></div>

        <div x-show="isModalOpen"
             wire:ignore.self
             class="bg-surface-container-lowest rounded-[40px] shadow-2xl ring-1 ring-white/10 w-full max-w-6xl overflow-hidden relative z-10 flex flex-col h-[92vh]"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0">
             
                <form wire:submit.prevent="save" class="flex flex-col h-full">
                    <!-- Modal Header with Tabs -->
                    <div class="shrink-0 bg-surface-container-low/30 border-b border-outline-variant/10">
                        <div class="p-8 pb-4 flex justify-between items-center">
                            <h4 class="font-headline text-2xl text-on-surface italic font-bold leading-tight">
                                {{ $editingId ? 'Edit Exam Question' : 'New Exam Question' }}
                            </h4>
                            <button type="button" @click="isModalOpen = false; $wire.resetForm()" class="text-stone-400 hover:text-on-surface transition-colors p-2">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        
                        <div class="flex px-8 overflow-x-auto no-scrollbar">
                            <button type="button" @click="activeTab = 'question'" :class="activeTab === 'question' ? 'border-primary text-primary' : 'border-transparent text-stone-400 hover:text-stone-600'" class="px-6 py-4 border-b-2 font-bold text-[10px] uppercase tracking-widest transition-all whitespace-nowrap">1. Question Details</button>
                            <button type="button" @click="activeTab = 'tags'" :class="activeTab === 'tags' ? 'border-primary text-primary' : 'border-transparent text-stone-400 hover:text-stone-600'" class="px-6 py-4 border-b-2 font-bold text-[10px] uppercase tracking-widest transition-all whitespace-nowrap">2. Taxonomy & Tags</button>
                            <button type="button" @click="activeTab = 'guidelines'" :class="activeTab === 'guidelines' ? 'border-primary text-primary' : 'border-transparent text-stone-400 hover:text-stone-600'" class="px-6 py-4 border-b-2 font-bold text-[10px] uppercase tracking-widest transition-all whitespace-nowrap">3. Answering Guidelines</button>
                            <button type="button" @click="activeTab = 'model'" :class="activeTab === 'model' ? 'border-primary text-primary' : 'border-transparent text-stone-400 hover:text-stone-600'" class="px-6 py-4 border-b-2 font-bold text-[10px] uppercase tracking-widest transition-all whitespace-nowrap">4. Model Answer</button>
                            <button type="button" @click="activeTab = 'rubric'" :class="activeTab === 'rubric' ? 'border-primary text-primary' : 'border-transparent text-stone-400 hover:text-stone-600'" class="px-6 py-4 border-b-2 font-bold text-[10px] uppercase tracking-widest transition-all whitespace-nowrap">5. Scoring & Resources</button>
                            <button type="button" @click="activeTab = 'publishing'" :class="activeTab === 'publishing' ? 'border-primary text-primary' : 'border-transparent text-stone-400 hover:text-stone-600'" class="px-6 py-4 border-b-2 font-bold text-[10px] uppercase tracking-widest transition-all whitespace-nowrap">6. Publishing</button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto no-scrollbar p-10">
                        @error('save_error')
                            <div class="p-4 bg-error/10 text-error rounded-2xl text-xs mb-8 flex items-center gap-3 border border-error/20">
                                <span class="material-symbols-outlined text-sm">warning</span>
                                {{ $message }}
                            </div>
                        @enderror

                        <!-- TAB: Question Details -->
                        <div x-show="activeTab === 'question'" class="space-y-10 animate-in fade-in duration-300">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                                <div class="lg:col-span-8 space-y-8">
                                    <div class="space-y-3">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Title</label>
                                        <input wire:model="title" type="text" class="w-full bg-surface-container-low border border-transparent rounded-2xl p-5 text-lg font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. 2024 Mains: Cultural Relativism Inquiry">
                                    </div>
                                    <div class="space-y-3">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Actual Question Text (Required)</label>
                                        <textarea wire:model="question_text" rows="8" class="w-full bg-surface-container-low border border-transparent rounded-2xl p-6 text-xl font-headline italic leading-relaxed text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="Enter the exact UPSC question text here..."></textarea>
                                        @error('question_text') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-3">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Contextual Intro / Hook (Optional)</label>
                                        <textarea wire:model="short_context" rows="3" class="w-full bg-surface-container-low border border-transparent rounded-2xl p-5 text-sm leading-relaxed text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="Provide a brief context or relevance for this question..."></textarea>
                                    </div>
                                </div>
                                
                                <div class="lg:col-span-4 space-y-8">
                                    <div class="bg-surface-container-low/30 border border-outline-variant/10 rounded-[28px] p-8 space-y-6">
                                        <h5 class="text-[10px] uppercase font-bold text-primary tracking-widest border-b border-primary/10 pb-4">Exam Metadata</h5>
                                        
                                        <div class="space-y-2">
                                            <label class="text-[9px] uppercase font-bold text-on-surface-variant tracking-widest">Exam Type</label>
                                            <div class="bg-white border border-outline-variant/20 rounded-xl px-4 py-3 text-xs font-bold text-on-surface shadow-sm">
                                                UPSC
                                            </div>
                                            <input type="hidden" wire:model="form_exam_type" value="UPSC">
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-[9px] uppercase font-bold text-on-surface-variant tracking-widest">Year</label>
                                            <input wire:model="form_year" type="text" class="w-full bg-white border border-outline-variant/20 rounded-xl px-4 py-3 text-xs font-bold shadow-sm" placeholder="e.g. 2024">
                                        </div>
                                    </div>
                                    
                                    <div class="bg-surface-container-low/30 border border-outline-variant/10 rounded-[28px] p-8 space-y-6">
                                        <h5 class="text-[10px] uppercase font-bold text-primary tracking-widest border-b border-primary/10 pb-4">Writing Targets</h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="text-[9px] uppercase font-bold text-on-surface-variant tracking-widest">Marks</label>
                                                <input wire:model="marks" type="number" class="w-full bg-white border border-outline-variant/20 rounded-xl px-4 py-3 text-xs font-bold shadow-sm">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[9px] uppercase font-bold text-on-surface-variant tracking-widest">Word Limit</label>
                                                <input wire:model="word_limit" type="number" class="w-full bg-white border border-outline-variant/20 rounded-xl px-4 py-3 text-xs font-bold shadow-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Taxonomy & Tags -->
                        <div x-show="activeTab === 'tags'" class="animate-in fade-in duration-300">
                            <div class="max-w-3xl mx-auto space-y-8">
                                <div class="bg-surface-container-low/30 border border-outline-variant/10 rounded-[32px] p-10">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="h-10 w-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                            <span class="material-symbols-outlined">label</span>
                                        </div>
                                        <div>
                                            <h5 class="font-headline text-xl font-bold italic">Question Taxonomy</h5>
                                            <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-1">Categorize this question for precise user discoverability</p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-10">
                                        <x-admin.tag-selector id="exam-tag-selector" wire:model="selectedTags" :modelClass="\App\Models\Exam\ExamQuestion::class" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Answering Guidelines -->
                        <div x-show="activeTab === 'guidelines'" class="animate-in fade-in duration-300">
                            <div class="max-w-4xl mx-auto space-y-6">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <h5 class="font-headline text-xl font-bold italic">Writing Guidelines</h5>
                                        <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-1">Steps and tips displayed during the writing process</p>
                                    </div>
                                    <span class="text-[9px] uppercase font-bold text-stone-400 bg-stone-50 px-3 py-1 rounded-full border border-stone-200">Rich Text Editor</span>
                                </div>
                                <div class="rounded-[32px] overflow-hidden border border-outline-variant/10 shadow-inner">
                                    <x-markdown-editor wire:model="answer_guidelines" :wire:key="'guidelines-'.$editingId" />
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Model Answer -->
                        <div x-show="activeTab === 'model'" class="animate-in fade-in duration-300 space-y-12">
                            <div class="max-w-5xl mx-auto space-y-12">
                                <div class="space-y-6">
                                    <h5 class="font-headline text-xl font-bold italic">Ideal Model Answer</h5>
                                    <div class="rounded-[32px] overflow-hidden border border-outline-variant/10 shadow-inner">
                                        <x-markdown-editor wire:model="model_answer" :wire:key="'model-'.$editingId" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Scoring & Resources -->
                        <div x-show="activeTab === 'rubric'" class="animate-in fade-in duration-300 space-y-12">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <!-- Evaluation Scoring Criteria -->
                                <div class="space-y-6">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-headline text-xl font-bold italic">Scoring Criteria</h5>
                                            <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-1">Breakdown of scoring for <span class="text-primary font-bold">{{ $marks }} Marks</span></p>
                                        </div>
                                        <button type="button" wire:click="addRubricRow" class="text-primary text-[10px] font-bold uppercase tracking-widest flex items-center gap-1 hover:underline">
                                            <span class="material-symbols-outlined text-sm">add</span> Add Criterion
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        @foreach($evaluation_rubric as $index => $row)
                                            <div class="flex gap-4 items-start bg-stone-50 p-4 rounded-2xl border border-stone-100 animate-in slide-in-from-left-4 duration-300">
                                                <div class="flex-1 space-y-3">
                                                    <input wire:model="evaluation_rubric.{{ $index }}.criteria" type="text" placeholder="Criteria Title (e.g. Theoretical Depth)" class="w-full bg-white border-none rounded-xl px-4 py-2 text-[11px] font-bold shadow-sm">
                                                    <input wire:model="evaluation_rubric.{{ $index }}.marks" type="text" placeholder="Max Marks (e.g. 4)" class="w-full bg-white border-none rounded-xl px-4 py-2 text-[11px] font-mono shadow-sm">
                                                </div>
                                                <button type="button" wire:click="removeRubricRow({{ $index }})" class="p-2 text-stone-300 hover:text-error transition-colors">
                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                </button>
                                            </div>
                                        @endforeach
                                        @if(empty($evaluation_rubric))
                                            <div class="py-8 text-center border-2 border-dashed border-stone-100 rounded-[32px]">
                                                <p class="text-[10px] uppercase font-bold text-stone-300 tracking-widest">No criteria added.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Learning Resources -->
                                <div class="space-y-6">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-headline text-xl font-bold italic">Linked Study Material</h5>
                                            <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-1">Direct links to modules, videos, or library books</p>
                                        </div>
                                        <button type="button" wire:click="addResourceRow" class="text-primary text-[10px] font-bold uppercase tracking-widest flex items-center gap-1 hover:underline">
                                            <span class="material-symbols-outlined text-sm">add</span> Add Link
                                        </button>
                                    </div>

                                    <div class="space-y-6">
                                        @foreach($learning_resources as $index => $res)
                                            <div class="bg-stone-50 p-6 rounded-3xl border border-stone-100 space-y-5 animate-in slide-in-from-right-4 duration-300 relative group">
                                                <div class="flex justify-between items-center">
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-8 w-8 bg-white rounded-lg flex items-center justify-center text-primary shadow-sm">
                                                            <span class="material-symbols-outlined text-[18px]">
                                                                {{ $res['type'] === 'Lesson Video' ? 'play_circle' : ($res['type'] === 'Library Resource' ? 'library_books' : 'school') }}
                                                            </span>
                                                        </div>
                                                        <select wire:model.live="learning_resources.{{ $index }}.type" class="bg-white border-none rounded-lg px-3 py-1.5 text-[9px] font-bold uppercase tracking-widest text-primary shadow-sm focus:ring-2 focus:ring-primary/20">
                                                            <option value="Course Module">Course Module</option>
                                                            <option value="Lesson Video">Lesson Video</option>
                                                            <option value="Module Resource (PDF)">Module Resource (PDF)</option>
                                                            <option value="Library Resource">Library Resource</option>
                                                        </select>
                                                    </div>
                                                    <button type="button" wire:click="removeResourceRow({{ $index }})" class="p-1 text-stone-300 hover:text-error transition-colors">
                                                        <span class="material-symbols-outlined text-sm">close</span>
                                                    </button>
                                                </div>

                                                <div class="space-y-4">
                                                    @if($res['type'] === 'Course Module')
                                                        <div class="space-y-2">
                                                            <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">Select LMS Module</label>
                                                            <select wire:model.live="learning_resources.{{ $index }}.id" class="w-full bg-white border border-stone-100 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                                <option value="">Choose a module...</option>
                                                                @foreach($allModules as $module)
                                                                    <option value="{{ $module->id }}">{{ $module->title }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @elseif($res['type'] === 'Lesson Video')
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div class="space-y-2">
                                                                <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">1. Select Module</label>
                                                                <select wire:model.live="learning_resources.{{ $index }}.module_id" class="w-full bg-white border border-stone-100 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                                    <option value="">Choose module...</option>
                                                                    @foreach($allModules as $module)
                                                                        <option value="{{ $module->id }}">{{ $module->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="space-y-2">
                                                                <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">2. Select Video</label>
                                                                <select wire:model.live="learning_resources.{{ $index }}.id" class="w-full bg-white border border-stone-100 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20" {{ empty($res['module_id']) ? 'disabled' : '' }}>
                                                                    <option value="">Choose video...</option>
                                                                    @if(!empty($res['module_id']))
                                                                        @foreach($allLessons->where('lms_module_id', $res['module_id']) as $lesson)
                                                                            <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @elseif($res['type'] === 'Module Resource (PDF)')
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div class="space-y-2">
                                                                <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">1. Select Module</label>
                                                                <select wire:model.live="learning_resources.{{ $index }}.module_id" class="w-full bg-white border border-stone-100 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                                    <option value="">Choose module...</option>
                                                                    @foreach($allModules as $module)
                                                                        <option value="{{ $module->id }}">{{ $module->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="space-y-2">
                                                                <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">2. Select PDF Resource</label>
                                                                <select wire:model.live="learning_resources.{{ $index }}.id" class="w-full bg-white border border-stone-100 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20" {{ empty($res['module_id']) ? 'disabled' : '' }}>
                                                                    <option value="">Choose resource...</option>
                                                                    @if(!empty($res['module_id']))
                                                                        @foreach($allLmsResources->where('lms_module_id', $res['module_id']) as $lmsResource)
                                                                            <option value="{{ $lmsResource->id }}">{{ $lmsResource->title }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @elseif($res['type'] === 'Library Resource')
                                                        <div class="space-y-2">
                                                            <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">Select Book / Paper</label>
                                                            <select wire:model.live="learning_resources.{{ $index }}.id" class="w-full bg-white border border-stone-100 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                                <option value="">Choose a resource...</option>
                                                                @foreach($allLibraryResources as $libraryResource)
                                                                    <option value="{{ $libraryResource->id }}">{{ $libraryResource->title }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif

                                                    <input wire:model="learning_resources.{{ $index }}.description" type="text" placeholder="Custom note (e.g. Refer to Chapter 4)" class="w-full bg-white border border-stone-100 rounded-xl px-4 py-2.5 text-[10px] font-medium shadow-sm focus:ring-2 focus:ring-primary/20">
                                                </div>
                                            </div>
                                        @endforeach
                                        @if(empty($learning_resources))
                                            <div class="py-12 text-center border-2 border-dashed border-stone-100 rounded-[40px] bg-stone-50/50">
                                                <span class="material-symbols-outlined text-4xl text-stone-200 mb-2">auto_stories</span>
                                                <p class="text-[10px] uppercase font-bold text-stone-300 tracking-widest">No study materials linked.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Publishing -->
                        <div x-show="activeTab === 'publishing'" class="animate-in fade-in duration-300">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <!-- Left Column: Question Type (Main Configuration) -->
                                <div class="lg:col-span-7 bg-stone-50/50 rounded-[2rem] p-6 border border-stone-100 flex flex-col justify-center">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center text-lg">
                                            <i class="ri-question-answer-line"></i>
                                        </div>
                                        <h4 class="font-headline italic text-lg font-bold leading-tight">Define Question Mode</h4>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <label class="exam-question-type-option {{ $question_kind === 'model' ? 'active' : '' }}">
                                            <input type="radio" class="d-none" wire:model.live="question_kind" value="model">
                                            <div class="option-card p-4 rounded-2xl border bg-white h-full flex flex-col justify-between">
                                                <div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <div class="option-icon !w-8 !h-8 !text-base !rounded-lg shrink-0">
                                                            <i class="ri-edit-2-line"></i>
                                                        </div>
                                                        <h5 class="font-bold text-xs uppercase tracking-wider">Model</h5>
                                                    </div>
                                                    <p class="text-stone-500 text-[10px] leading-relaxed mb-3 italic">Answer practice with timer and submissions.</p>
                                                </div>
                                                <div class="flex flex-wrap gap-1">
                                                    <span class="badge bg-primary/5 text-primary text-[7px] px-1.5 py-0.5 rounded uppercase tracking-tighter">Practice</span>
                                                    <span class="badge bg-success/5 text-success text-[7px] px-1.5 py-0.5 rounded uppercase tracking-tighter">Timer</span>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="exam-question-type-option {{ $question_kind === 'past' ? 'active' : '' }}">
                                            <input type="radio" class="d-none" wire:model.live="question_kind" value="past">
                                            <div class="option-card p-4 rounded-2xl border bg-white h-full flex flex-col justify-between">
                                                <div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <div class="option-icon !w-8 !h-8 !text-base !rounded-lg shrink-0">
                                                            <i class="ri-archive-line"></i>
                                                        </div>
                                                        <h5 class="font-bold text-xs uppercase tracking-wider">Past</h5>
                                                    </div>
                                                    <p class="text-stone-500 text-[10px] leading-relaxed mb-3 italic">Reference question with model answer only.</p>
                                                </div>
                                                <div class="flex flex-wrap gap-1">
                                                    <span class="badge bg-warning/5 text-warning text-[7px] px-1.5 py-0.5 rounded uppercase tracking-tighter">Past Paper</span>
                                                    <span class="badge bg-dark/5 text-dark text-[7px] px-1.5 py-0.5 rounded uppercase tracking-tighter">Read Only</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Right Column: Publishing & Access -->
                                <div class="lg:col-span-5 space-y-4">
                                    <!-- Status -->
                                    <div class="bg-white rounded-[2rem] p-6 border border-stone-100">
                                        <h5 class="text-[9px] uppercase font-bold text-on-surface-variant tracking-widest mb-4 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-xs">rocket_launch</span> Status
                                        </h5>
                                        <div class="grid grid-cols-3 gap-2">
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" wire:model="form_status" value="published" class="peer sr-only">
                                                <div class="py-3 text-center rounded-xl border-2 border-stone-50 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                                    <p class="text-[8px] uppercase font-bold tracking-widest text-stone-400 group-peer-checked:text-primary">Live</p>
                                                </div>
                                            </label>
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" wire:model="form_status" value="draft" class="peer sr-only">
                                                <div class="py-3 text-center rounded-xl border-2 border-stone-50 peer-checked:border-stone-400 peer-checked:bg-stone-50 transition-all">
                                                    <p class="text-[8px] uppercase font-bold tracking-widest text-stone-400 group-peer-checked:text-stone-600">Draft</p>
                                                </div>
                                            </label>
                                            <label class="relative cursor-pointer group">
                                                <input type="radio" wire:model="form_status" value="archived" class="peer sr-only">
                                                <div class="py-3 text-center rounded-xl border-2 border-stone-50 peer-checked:border-warning peer-checked:bg-warning/5 transition-all">
                                                    <p class="text-[8px] uppercase font-bold tracking-widest text-stone-400 group-peer-checked:text-warning">Archived</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Access Control -->
                                    <div class="bg-white rounded-[2rem] p-6 border border-stone-100">
                                        <h5 class="text-[9px] uppercase font-bold text-on-surface-variant tracking-widest mb-4 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-xs">security</span> Visibility
                                        </h5>
                                        <div class="flex gap-2">
                                            <button type="button" wire:click="$set('access_type', 'public')" 
                                                    class="flex-1 py-3 rounded-xl border transition-all text-[9px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 {{ $access_type === 'public' ? 'bg-primary/5 border-primary/20 text-primary' : 'bg-white border-stone-100 text-stone-400' }}">
                                                <span class="material-symbols-outlined text-xs">public</span> Public
                                            </button>
                                            <button type="button" wire:click="$set('access_type', 'member_only')" 
                                                    class="flex-1 py-3 rounded-xl border transition-all text-[9px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 {{ $access_type === 'member_only' ? 'bg-secondary/5 border-secondary/20 text-secondary' : 'bg-white border-stone-100 text-stone-400' }}">
                                                <span class="material-symbols-outlined text-xs">lock</span> Members
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-8 border-t border-outline-variant/10 bg-surface-container-low/30 flex justify-between items-center gap-4 shrink-0">
                        <div class="flex items-center gap-3">
                            <button type="button" @click="isModalOpen = false; $wire.resetForm()" class="px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Cancel</button>
                            
                            @if($activeTab !== 'question')
                                <button type="button" wire:click="prevStep" class="px-6 py-3 border border-outline-variant/20 rounded-xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                                    Previous
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            @if($activeTab !== 'publishing')
                                <button type="button" wire:click="nextStep" class="bg-stone-800 text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-black transition-all flex items-center gap-2">
                                    Next Step
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </button>
                            @endif

                            @if($editingId || $activeTab === 'publishing')
                                <button type="submit" class="bg-primary text-on-primary px-12 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2">
                                    <span wire:loading wire:target="save" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                                    {{ $editingId ? 'Update Question' : 'Save Question' }}
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .exam-question-type-option {
        display: block;
        cursor: pointer;
    }

    .exam-question-type-option .option-card {
        background: #fff;
        border-color: rgba(0, 0, 0, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }

    .exam-question-type-option:hover .option-card {
        transform: translateY(-2px);
        border-color: rgba(154, 52, 18, 0.35) !important;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .exam-question-type-option.active .option-card {
        border-color: #9a3412 !important;
        background: rgba(154, 52, 18, 0.04);
        box-shadow: 0 14px 35px rgba(154, 52, 18, 0.12);
    }

    .exam-question-type-option .option-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: rgba(154, 52, 18, 0.10);
        color: #9a3412;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .exam-question-type-option.active .option-icon {
        background: #9a3412;
        color: #fff;
    }
</style>
