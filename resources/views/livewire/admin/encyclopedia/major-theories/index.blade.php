<div x-data="{ isModalOpen: false }" @open-modal.window="isModalOpen = true; document.body.style.overflow = 'hidden'" @close-modal.window="isModalOpen = false; document.body.style.overflow = 'auto'" class="relative">
    
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
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Major Theories</h1>
            <p class="font-body text-on-surface-variant text-lg">Manage the encyclopedia of prominent theories and paradigms.</p>
        </div>
        <button type="button" @click="$dispatch('open-modal')" wire:click="openModal" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span>
            Add Theory
        </button>
    </div>

    <!-- Management Controls -->
    <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden mb-8">
        <div class="p-6 border-b border-outline-variant/10 flex flex-wrap gap-4 items-center justify-between bg-surface-container-low/30">
            <div class="flex gap-4 items-center flex-1 min-w-[300px]">
                <div class="relative flex-1 max-w-sm">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-outline-variant/20 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm" placeholder="Search theories..." type="text"/>
                </div>
                <select wire:model.live="statusFilter" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low/50">
                    <tr>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Theory Title</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Key Thinkers</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Status</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($theories as $theory)
                        <tr class="hover:bg-surface-container-low/20 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-headline font-bold text-on-surface mb-0.5 leading-tight">{{ $theory->title }}</p>
                                <p class="text-[10px] text-stone-400 font-mono italic">{{ $theory->slug }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-on-surface-variant">
                                {{ $theory->key_thinkers_text ?: '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase {{ $theory->status == 'active' ? 'bg-primary-container text-on-primary-container' : 'bg-surface-container-highest text-on-surface-variant' }}">
                                    {{ $theory->status }}
                                </span>
                            </td>
                             <td class="px-6 py-4 text-right whitespace-nowrap overflow-visible">
                                 <div x-data="{ open: false }" class="relative inline-flex justify-end items-center">
                                     <button @click="open = !open" @click.away="open = false" class="p-2 text-stone-400 hover:text-on-surface transition-colors rounded-full hover:bg-surface-container-high">
                                         <span class="material-symbols-outlined text-sm">more_vert</span>
                                     </button>
                                     
                                     <div x-show="open" 
                                          x-transition:enter="transition ease-out duration-200"
                                          x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                                          x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                          x-transition:leave="transition ease-in duration-100"
                                          x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                          x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                                          x-cloak
                                          class="absolute right-0 top-1/2 -translate-y-1/2 mr-10 w-48 bg-surface-container-lowest rounded-xl shadow-2xl border border-outline-variant/20 z-[100] overflow-hidden">
                                         <button type="button" @click="open = false; $dispatch('open-modal')" wire:click="openModal({{ $theory->id }})" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                             <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                                             Edit Details
                                         </button>
                                         <button type="button" @click="open = false; $dispatch('open-delete-modal', { 
                                                     title: 'Delete Theory', 
                                                     message: 'Permanent removal of this theory from the archive. Proceed?', 
                                                     action: { type: 'livewire', component: '{{ $this->getId() }}', method: 'delete', params: [{{ $theory->id }}] } 
                                                 })" 
                                                 class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-error hover:bg-error/5 transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                             <span class="material-symbols-outlined text-sm">delete</span>
                                             Delete Theory
                                         </button>
                                     </div>
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant/30 mb-2">account_tree</span>
                                    <p class="text-on-surface-variant font-headline text-2xl italic font-bold">No Theories Found.</p>
                                    <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-2 max-w-xs leading-relaxed">Adjust your filters or add a new major theory to expand the encyclopedia.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($theories->hasPages())
            <div class="px-6 py-6 border-t border-outline-variant/10 bg-surface-container-low/10">
                {{ $theories->links() }}
            </div>
        @endif
    </div>

    <!-- Livewire Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
        <div x-show="isModalOpen" 
             style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);"
             class="fixed inset-0 transition-opacity"
             @click="$dispatch('close-modal'); $wire.closeModal()"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div x-show="isModalOpen"
             wire:ignore.self
             class="bg-surface-container-lowest rounded-[32px] shadow-2xl ring-1 ring-white/10 w-full max-w-5xl overflow-hidden relative z-10 flex flex-col max-h-[90vh]"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100 translate-y-0"
             x-transition:leave-end="scale-95 opacity-0 translate-y-4">
             
                <form wire:submit.prevent="save" class="flex flex-col h-full min-h-0">
                    <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30 shrink-0">
                        <div>
                            <h4 class="font-headline text-2xl text-on-surface italic font-bold leading-tight">{{ $theoryId ? 'Edit Major Theory' : 'New Major Theory' }}</h4>
                        </div>
                        <button type="button" @click="$dispatch('close-modal'); $wire.closeModal()" class="text-stone-400 hover:text-on-surface transition-colors p-2">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-10 space-y-8 no-scrollbar min-h-0">
                        <div class="grid grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Theory Title</label>
                                <input wire:model="title" type="text" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-sm font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none">
                                @error('title') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Key Thinkers</label>
                                <input wire:model="key_thinkers_text" type="text" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-sm text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. Franz Boas, Bronisław Malinowski">
                                @error('key_thinkers_text') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Short Description</label>
                            <textarea wire:model="short_description" rows="2" class="w-full bg-surface-container-low border border-transparent rounded-xl p-5 text-sm leading-relaxed text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none resize-none"></textarea>
                            @error('short_description') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Status</label>
                            <select wire:model="status" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-xs font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none cursor-pointer">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Body Text (Markdown)</label>
                            <div class="border border-outline-variant/20 rounded-2xl overflow-hidden shadow-inner">
                                <x-markdown-editor wire:model="body_markdown" :wire:key="'editor-'.$modalSessionId" />
                            </div>
                        </div>
                        @error('body_markdown') <span class="text-[10px] text-error font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="p-8 border-t border-outline-variant/10 bg-surface-container-low/30 flex justify-end items-center gap-4 shrink-0">
                        <button type="button" @click="$dispatch('close-modal'); $wire.closeModal()" class="px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Cancel</button>
                        <button type="submit" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                            Save Theory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
