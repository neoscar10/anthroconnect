<div class="relative">
    <!-- Header -->
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Global Resources Archive</h1>
            <p class="font-body text-on-surface-variant text-lg">Centralized repository for all scholarly documents and archival PDF materials.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 px-6 py-4 bg-primary/10 text-primary rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-primary/20 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                {{ session('success') }}
            </div>
            <button @click="location.reload()" class="hover:rotate-180 transition-transform duration-500">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    <!-- Content Table -->
    <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden">
        <div class="p-8 border-b border-outline-variant/10 flex flex-wrap justify-between items-center bg-surface-container-low/20 gap-4">
            <div class="flex items-center gap-4 flex-1 min-w-[250px]">
                <span class="material-symbols-outlined text-stone-400">filter_alt</span>
                <input wire:model.live="search" type="text" placeholder="Search archive for records..." class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder-stone-400 font-medium italic">
            </div>
            
            <div class="flex gap-2">
                <select wire:model.live="moduleFilter" class="bg-surface-container-low border-none rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer">
                    <option value="">All Learning Modules</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->id }}">{{ $m->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Resource Record</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Associated Module</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Type</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($resources as $res)
                        <tr class="hover:bg-surface-container-low/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-error/10 text-error rounded-xl flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-sm font-bold">picture_as_pdf</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="font-headline text-lg font-bold text-on-surface italic leading-tight">{{ $res->title }}</span>
                                            @if($res->is_members_only)
                                                <span class="material-symbols-outlined text-[14px] text-primary" title="Members Only Access">lock</span>
                                            @endif
                                        </div>
                                        <span class="text-[9px] text-stone-400 italic mt-0.5 line-clamp-1">{{ $res->short_description ?: 'No abstract provided.' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <a href="{{ route('admin.lms.modules.edit', $res->module) }}" class="flex flex-col gap-1 hover:text-primary transition-colors group/link">
                                    <span class="text-[10px] font-bold uppercase tracking-widest">{{ $res->module->title }}</span>
                                    <span class="text-[9px] text-stone-400 italic flex items-center gap-1">Jump to Unit <span class="material-symbols-outlined text-[10px]">open_in_new</span></span>
                                </a>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-stone-500">Digital Archive</span>
                                    <span class="text-[9px] text-stone-400 italic mt-1 uppercase">{{ pathinfo($res->file_path, PATHINFO_EXTENSION) }} Document</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap overflow-visible">
                                <div x-data="{ open: false }" class="relative inline-flex justify-end items-center">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 text-stone-400 hover:text-on-surface transition-colors rounded-full hover:bg-surface-container-high transition-colors">
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
                                         class="absolute right-0 top-1/2 -translate-y-1/2 mr-10 w-48 bg-surface-container-lowest rounded-xl shadow-2xl border border-outline-variant/20 z-[100] overflow-hidden text-left">
                                        
                                        <a href="{{ Storage::url($res->file_path) }}" target="_blank" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-secondary">visibility</span>
                                            View Archive
                                        </a>

                                        <a href="{{ route('admin.lms.modules.edit', $res->module) }}" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                                            Manage within Module
                                        </a>
                                        
                                        <button type="button" wire:confirm="Archival de-registration will remove this document from the portal. Proceed?" wire:click="deleteResource({{ $res->id }})" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-error hover:bg-error/5 transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                            Prune Archive
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-32 text-center text-stone-300">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-6xl mb-4">inventory_2</span>
                                    <p class="font-headline text-2xl italic">The archives are thin.</p>
                                    <p class="text-[10px] uppercase tracking-widest mt-2">No scholarly documents identified matching your filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($resources->hasPages())
            <div class="px-8 py-6 border-t border-outline-variant/10 bg-surface-container-low/20">
                {{ $resources->links() }}
            </div>
        @endif
    </div>
</div>
