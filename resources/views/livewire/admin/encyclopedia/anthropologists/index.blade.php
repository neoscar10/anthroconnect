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
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Anthropologists Directory</h1>
            <p class="font-body text-on-surface-variant text-lg">Curate the master database of notable researchers, thinkers, and ethnographers.</p>
        </div>
        <button type="button" @click="$dispatch('open-modal')" wire:click="openModal" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">person_add</span>
            Add Anthropologist
        </button>
    </div>

    <!-- Management Controls -->
    <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden mb-8">
        <div class="p-6 border-b border-outline-variant/10 flex flex-wrap gap-4 items-center justify-between bg-surface-container-low/30">
            <div class="flex gap-4 items-center flex-1 min-w-[300px]">
                <div class="relative flex-1 max-w-sm">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-outline-variant/20 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm" placeholder="Search anthropologists..." type="text"/>
                </div>
                <select wire:model.live="statusFilter" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select wire:model.live="featuredFilter" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Prominence</option>
                    <option value="1">Featured Only</option>
                    <option value="0">Not Featured</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low/50">
                    <tr>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Anthropologist</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Domain</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Taxonomy/Topics</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Status</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($anthropologists as $person)
                        <tr class="hover:bg-surface-container-low/20 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 bg-stone-100 rounded-full overflow-hidden shrink-0 border border-outline-variant/20">
                                        @if($person->profile_image)
                                            <img src="{{ Storage::url($person->profile_image) }}" class="object-cover h-full w-full" alt="">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-stone-200 text-stone-400 font-bold font-headline">
                                                {{ substr($person->full_name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-headline font-bold text-on-surface mb-0.5 leading-tight flex items-center gap-2">
                                            {{ $person->full_name }}
                                            @if($person->is_featured)
                                                <span class="material-symbols-outlined text-primary text-[12px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-stone-400 font-mono italic">{{ $person->birth_year ? $person->birth_year . ' - ' : '' }}{{ $person->death_year ?? 'Present' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-on-surface">{{ $person->discipline_or_specialization ?: '-' }}</p>
                                <p class="text-[10px] text-stone-400">{{ $person->nationality ?: 'Unknown Location' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @php $tags = $person->topics->take(3); @endphp
                                    @foreach($tags as $topic)
                                        <span class="px-2 py-0.5 rounded bg-surface-container-high text-[9px] font-bold text-on-surface-variant">{{ $topic->name }}</span>
                                    @endforeach
                                    @if($person->topics->count() > 3)
                                        <span class="px-2 py-0.5 rounded bg-stone-100 text-[9px] font-bold text-stone-400">+{{ $person->topics->count() - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase {{ $person->status == 'active' ? 'bg-primary-container text-on-primary-container' : 'bg-surface-container-highest text-on-surface-variant' }}">
                                    {{ $person->status }}
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
                                         <button type="button" @click="open = false; $dispatch('open-modal')" wire:click="openModal({{ $person->id }})" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                             <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                                             Edit Profile
                                         </button>
                                         <button type="button" @click="open = false; $dispatch('open-delete-modal', { 
                                                     title: 'Delete Profile', 
                                                     message: 'Are you sure you want to delete this profile? This action is irreversible.', 
                                                     action: { type: 'livewire', component: '{{ $this->getId() }}', method: 'delete', params: [{{ $person->id }}] } 
                                                 })" 
                                                 class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-error hover:bg-error/5 transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                             <span class="material-symbols-outlined text-sm">delete</span>
                                             Delete Profile
                                         </button>
                                     </div>
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant/30 mb-2">groups</span>
                                    <p class="text-on-surface-variant font-headline text-2xl italic font-bold">No Figures Found.</p>
                                    <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-2 max-w-xs leading-relaxed">Adjust your filters or add a new anthropologist to the database.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($anthropologists->hasPages())
            <div class="px-6 py-6 border-t border-outline-variant/10 bg-surface-container-low/10">
                {{ $anthropologists->links() }}
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
             class="bg-surface-container-lowest rounded-[32px] shadow-2xl ring-1 ring-white/10 w-full max-w-6xl overflow-hidden relative z-10 flex flex-col h-[90vh]"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100 translate-y-0"
             x-transition:leave-end="scale-95 opacity-0 translate-y-4">
             
                <form wire:submit.prevent="save" class="flex flex-col h-full">
                    <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30 shrink-0">
                        <div>
                            <h4 class="font-headline text-2xl text-on-surface italic font-bold leading-tight">{{ $anthropologistId ? 'Edit Anthropologist Profile' : 'New Anthropologist Profile' }}</h4>
                        </div>
                        <button type="button" @click="$dispatch('close-modal'); $wire.closeModal()" class="text-stone-400 hover:text-on-surface transition-colors p-2">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto no-scrollbar p-10">
                        @if ($errors->any())
                            <div class="p-4 bg-error/10 text-error rounded-xl text-sm mb-6">
                                Please check the form below for errors.
                            </div>
                        @endif

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                            
                            <!-- Main Information (8 Col) -->
                            <div class="lg:col-span-8 space-y-10">
                                <div class="grid grid-cols-2 gap-8">
                                    <div class="space-y-4">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-4">Full Name</label>
                                        <input wire:model="full_name" type="text" required class="w-full bg-surface-container-low border border-transparent rounded-2xl p-6 text-2xl font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. Margaret Mead">
                                        @error('full_name') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-4">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-4">Specialization</label>
                                        <input wire:model="discipline_or_specialization" type="text" class="w-full bg-surface-container-low border border-transparent rounded-2xl p-6 text-2xl font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. Cultural Anthropology">
                                        @error('discipline_or_specialization') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-8 px-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Origin / Nationality</label>
                                        <input wire:model="nationality" type="text" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-xs font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. American">
                                        @error('nationality') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Birth Year</label>
                                        <input wire:model="birth_year" type="number" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-xs text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none">
                                        @error('birth_year') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Death Year</label>
                                        <input wire:model="death_year" type="number" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-xs text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none">
                                        @error('death_year') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="space-y-2 px-4">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Profile Summary</label>
                                    <textarea wire:model="summary" rows="3" required class="w-full bg-surface-container-low border border-transparent rounded-2xl p-5 text-sm leading-relaxed text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none resize-none" placeholder="Short description..."></textarea>
                                    @error('summary') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-4 px-4">
                                    <div class="flex justify-between items-center">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Biography Context</label>
                                        <span class="text-[9px] uppercase font-bold text-stone-400 bg-stone-50 px-2 py-0.5 rounded border">Markdown Enabled</span>
                                    </div>
                                    <div class="rounded-[28px] overflow-hidden border border-outline-variant/10 shadow-inner">
                                        <x-markdown-editor wire:model="biography_markdown" :wire:key="'editor-'.$modalSessionId" />
                                    </div>
                                    @error('biography_markdown') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Meta Data (4 Col) -->
                            <div class="lg:col-span-4 space-y-10">
                                
                                <!-- Profile Picture -->
                                <div class="bg-surface-container-low/30 border border-outline-variant/10 rounded-[28px] p-8 space-y-4">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest block text-center">Portrait Avatar</label>
                                    <div class="flex flex-col items-center">
                                        <div class="relative w-32 h-32 bg-stone-100 rounded-full overflow-hidden border border-stone-200">
                                            @if($profile_image && !is_string($profile_image))
                                                <img src="{{ $profile_image->temporaryUrl() }}" class="object-cover w-full h-full">
                                            @elseif($anthropologistId && is_string($profile_image))
                                                <img src="{{ Storage::url($profile_image) }}" class="object-cover w-full h-full">
                                            @else
                                                <div class="absolute inset-0 flex flex-col items-center justify-center p-2 text-center bg-stone-100">
                                                    <span class="material-symbols-outlined text-3xl text-stone-300">account_circle</span>
                                                </div>
                                            @endif
                                            <input type="file" wire:model="profile_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                                        </div>
                                        <p class="text-[9px] text-stone-400 italic mt-3 text-center">Square ratio is best. Max 2MB.</p>
                                        @error('profile_image') <span class="text-[10px] text-error font-medium text-center mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Visibility & Prominence -->
                                <div class="bg-surface-container-low/30 border border-outline-variant/10 rounded-[28px] p-8 space-y-6">
                                    <div class="space-y-4">
                                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest block">Publication Status</label>
                                        <select wire:model="status" class="w-full bg-white border border-outline-variant/20 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                                            <option value="active">Active Entry</option>
                                            <option value="inactive">Archived / Draft</option>
                                        </select>
                                        @error('status') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="pt-2">
                                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                            <div class="relative inline-flex items-center">
                                                <input type="checkbox" wire:model="is_featured" class="sr-only peer">
                                                <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Mark as Featured Figure</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Relationships: Core Concepts -->
                                <div class="bg-surface-container-low/30 border border-outline-variant/10 rounded-[28px] p-8 space-y-4">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest block">Related Core Concepts</label>
                                    <div class="max-h-48 overflow-y-auto no-scrollbar border rounded-xl bg-white p-3 space-y-2">
                                        @foreach($coreConcepts as $concept)
                                            <label class="flex items-start gap-3 cursor-pointer p-2 rounded hover:bg-stone-50">
                                                <input type="checkbox" wire:model="selectedCoreConcepts" value="{{ $concept->id }}" class="mt-0.5 rounded border-stone-300 text-primary focus:ring-primary">
                                                <div>
                                                    <span class="text-xs font-bold text-on-surface block">{{ $concept->title }}</span>
                                                    @if($concept->short_description)
                                                        <span class="text-[9px] text-stone-500 block truncate w-40">{{ $concept->short_description }}</span>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                        @if($coreConcepts->isEmpty())
                                            <p class="text-xs text-stone-400 italic p-2">No concepts available.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Relationships: Topics -->
                                <div class="bg-surface-container-low/30 border border-outline-variant/10 rounded-[28px] p-8 space-y-4">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest block">Assigned Topics</label>
                                    
                                    <div class="max-h-40 overflow-y-auto no-scrollbar border rounded-xl bg-white p-3 space-y-2">
                                        @foreach($topics as $topic)
                                            <label class="flex items-center gap-3 cursor-pointer p-1.5 rounded hover:bg-stone-50">
                                                <input type="checkbox" wire:model="selectedTopics" value="{{ $topic->id }}" class="rounded border-stone-300 text-primary focus:ring-primary">
                                                <span class="text-xs font-bold text-on-surface block">{{ $topic->name }}</span>
                                            </label>
                                        @endforeach
                                        @if($topics->isEmpty())
                                            <p class="text-xs text-stone-400 italic p-2 hidden">No existing topics.</p>
                                        @endif
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-outline-variant/20 space-y-3">
                                        <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest block">Add Custom Topics (Inline)</label>
                                        <div class="flex gap-2">
                                            <input wire:model="newTopicInput" type="text" class="flex-1 bg-white border border-outline-variant/20 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-primary shadow-sm" placeholder="Ex: Kinship">
                                            <button type="button" wire:click="addNewTopic" class="bg-stone-200 text-stone-600 px-3 py-2 rounded-lg text-xs font-bold hover:bg-stone-300 transition-colors">Add</button>
                                        </div>
                                        
                                        @if(count($newTopicsList) > 0)
                                            <div class="flex flex-wrap gap-2 mt-3">
                                                @foreach($newTopicsList as $index => $newTopic)
                                                    <div class="bg-primary/10 text-primary text-[10px] px-2 py-1 rounded flex items-center gap-1 font-bold">
                                                        {{ $newTopic }}
                                                        <button type="button" wire:click="removeNewTopic({{ $index }})" class="hover:text-error transition-colors"><span class="material-symbols-outlined text-[12px]">close</span></button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 border-t border-outline-variant/10 bg-surface-container-low/30 flex justify-end items-center gap-4 shrink-0">
                        <button type="button" @click="$dispatch('close-modal'); $wire.closeModal()" class="px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Cancel</button>
                        <button type="submit" class="bg-primary text-on-primary px-12 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                            Save Anthropologist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
