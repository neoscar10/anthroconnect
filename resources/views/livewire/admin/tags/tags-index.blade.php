<div>
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-headline italic text-on-surface">Tags Management</h1>
            <p class="text-on-surface-variant">Organize and manage classification tags for all content.</p>
        </div>
        <button wire:click="openGroupModal()" class="bg-primary text-on-primary px-6 py-2 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined text-sm">add</span>
            New Tag Group
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-primary/10 border border-primary/20 text-primary rounded-xl text-xs font-bold uppercase tracking-widest flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-error/10 border border-error/20 text-error rounded-xl text-xs font-bold uppercase tracking-widest flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="flex gap-8">
        <!-- Left Panel: Tag Groups -->
        <div class="w-80 flex flex-col gap-4 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                <input wire:model.live="groupSearch" type="text" placeholder="Search groups..." class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant/20 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>

            <div class="space-y-2">
                @foreach($groups as $group)
                    <div wire:click="selectGroup({{ $group->id }})" 
                         class="cursor-pointer p-4 rounded-2xl border transition-all flex items-center gap-4 {{ $selectedGroupId == $group->id ? 'bg-primary/5 border-primary shadow-sm' : 'bg-surface-container-lowest border-outline-variant/10 hover:border-outline-variant/30' }}">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $selectedGroupId == $group->id ? 'bg-primary text-on-primary' : 'bg-stone-100 text-stone-400' }}">
                            <span class="material-symbols-outlined text-sm">folder</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-sm truncate {{ $selectedGroupId == $group->id ? 'text-primary' : 'text-on-surface' }}">{{ $group->name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[9px] uppercase tracking-tighter font-bold text-stone-400">{{ $group->tags_count }} Tags</span>
                                <span class="w-1 h-1 rounded-full bg-stone-300"></span>
                                <span class="text-[9px] uppercase tracking-tighter font-bold {{ $group->selection_type == 'single_select' ? 'text-secondary' : 'text-primary' }}">{{ str_replace('_', ' ', $group->selection_type) }}</span>
                            </div>
                        </div>
                        @if(!$group->is_active)
                            <span class="material-symbols-outlined text-xs text-error">visibility_off</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Panel: Tags -->
        <div class="flex-1 bg-surface-container-lowest rounded-3xl border border-outline-variant/10 flex flex-col shadow-sm">
            @if($selectedGroup)
                <div class="p-6 border-b border-outline-variant/10 bg-surface-container-low/30 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h2 class="text-xl font-headline font-bold text-on-surface">{{ $selectedGroup->name }}</h2>
                            <span class="px-2 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant text-[9px] font-bold uppercase tracking-widest">{{ str_replace('_', ' ', $selectedGroup->selection_type) }}</span>
                            @if(!$selectedGroup->is_active)
                                <span class="px-2 py-0.5 rounded-full bg-error/10 text-error text-[9px] font-bold uppercase tracking-widest">Inactive</span>
                            @endif
                        </div>
                        <p class="text-xs text-on-surface-variant">{{ $selectedGroup->description ?: 'No description provided.' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click="openGroupModal({{ $selectedGroup->id }})" class="p-2 text-stone-400 hover:text-primary hover:bg-primary/5 rounded-xl transition-all" title="Edit Group">
                            <span class="material-symbols-outlined text-sm">settings</span>
                        </button>
                        <button wire:click="openTagModal()" class="bg-primary text-on-primary px-4 py-2 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Add Tag
                        </button>
                    </div>
                </div>

                <div class="p-6 flex flex-col">
                    <div class="mb-6 relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                        <input wire:model.live="tagSearch" type="text" placeholder="Search tags in this group..." class="w-full pl-10 pr-4 py-2 bg-white border border-outline-variant/20 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                    </div>

                    <div class="space-y-3">
                        @forelse($tags as $tag)
                            <div class="group p-5 bg-white border border-outline-variant/10 rounded-2xl hover:border-primary/30 transition-all flex items-center gap-6">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-1">
                                        <h4 class="font-bold text-base text-on-surface truncate">{{ $tag->name }}</h4>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-mono bg-stone-100 text-stone-500">{{ $tag->slug }}</span>
                                        @if(!$tag->is_active)
                                            <span class="px-2 py-0.5 rounded bg-error/10 text-error text-[8px] font-bold uppercase tracking-widest">Inactive</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-on-surface-variant line-clamp-1 italic">{{ $tag->description ?: 'No description provided.' }}</p>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="openTagModal({{ $tag->id }})" class="w-10 h-10 flex items-center justify-center text-stone-400 hover:text-primary hover:bg-primary/5 rounded-xl transition-all" title="Edit Tag">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </button>
                                    <button wire:confirm="Are you sure you want to delete this tag?" wire:click="deleteTag({{ $tag->id }})" class="w-10 h-10 flex items-center justify-center text-stone-400 hover:text-error hover:bg-error/5 rounded-xl transition-all" title="Delete Tag">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-24 flex flex-col items-center justify-center text-stone-400">
                                <div class="w-20 h-20 rounded-full bg-stone-50 flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-4xl opacity-20">tag</span>
                                </div>
                                <p class="text-sm font-medium">No tags found in this group.</p>
                                <button wire:click="openTagModal()" class="mt-4 text-primary text-[10px] font-bold uppercase tracking-widest hover:underline">Create your first tag</button>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="mt-6">
                        {{ $tags->links() }}
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined text-6xl mb-4 opacity-10">folder_open</span>
                    <h3 class="text-xl font-headline italic">Select a Tag Group</h3>
                    <p class="text-sm mt-1">Choose a group from the left to manage its tags.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Group Modal -->
    <div x-data="{ open: @entangle('isGroupModalOpen') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm">
        <div @click.away="open = false" class="bg-surface-container-lowest w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-outline-variant/10">
            <div class="p-6 border-b border-outline-variant/10 flex items-center justify-between bg-surface-container-low/30">
                <h3 class="font-headline text-xl font-bold text-on-surface">{{ $groupId ? 'Edit' : 'Create' }} Tag Group</h3>
                <button @click="open = false" class="text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form wire:submit.prevent="saveGroup" class="p-6 space-y-6">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Group Name</label>
                    <input wire:model="groupName" type="text" class="w-full bg-stone-50 border border-outline-variant/20 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary transition-all" placeholder="e.g. Topics, Regions, Period">
                    @error('groupName') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Description</label>
                    <textarea wire:model="groupDescription" class="w-full bg-stone-50 border border-outline-variant/20 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary transition-all h-24 resize-none" placeholder="What is this group for?"></textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Selection Type</label>
                    <select wire:model="groupSelectionType" class="w-full bg-stone-50 border border-outline-variant/20 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary transition-all">
                        <option value="multi_select">Multiple Selection (Checkboxes)</option>
                        <option value="single_select">Single Selection (Radio Buttons)</option>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <input wire:model="groupIsActive" type="checkbox" id="groupActive" class="rounded border-outline-variant/30 text-primary focus:ring-primary">
                    <label for="groupActive" class="text-sm text-on-surface font-medium">Active (Visible on frontend)</label>
                </div>

                <div class="pt-4 flex items-center justify-between gap-4">
                    @if($groupId)
                        <button type="button" wire:confirm="Are you sure? This group will be deleted only if it has no tags." wire:click="deleteGroup({{ $groupId }})" class="text-error text-[10px] font-bold uppercase tracking-widest hover:underline">
                            Delete Group
                        </button>
                    @else
                        <div></div>
                    @endif
                    <div class="flex gap-3">
                        <button type="button" @click="open = false" class="px-6 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 transition-colors">Cancel</button>
                        <button type="submit" class="bg-primary text-on-primary px-8 py-2.5 rounded-xl font-bold uppercase tracking-widest text-[10px] shadow-lg shadow-primary/20">
                            {{ $groupId ? 'Update' : 'Create' }} Group
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tag Modal -->
    <div x-data="{ open: @entangle('isTagModalOpen') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm">
        <div @click.away="open = false" class="bg-surface-container-lowest w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-outline-variant/10">
            <div class="p-6 border-b border-outline-variant/10 flex items-center justify-between bg-surface-container-low/30">
                <h3 class="font-headline text-xl font-bold text-on-surface">{{ $tagId ? 'Edit' : 'Add' }} Tag in {{ $selectedGroup?->name }}</h3>
                <button @click="open = false" class="text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form wire:submit.prevent="saveTag" class="p-6 space-y-6">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Tag Name</label>
                    <input wire:model="tagName" type="text" class="w-full bg-stone-50 border border-outline-variant/20 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary transition-all" placeholder="e.g. Archeology, Asia, Functionalism">
                    @error('tagName') <span class="text-error text-[10px] mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Description</label>
                    <textarea wire:model="tagDescription" class="w-full bg-stone-50 border border-outline-variant/20 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary transition-all h-24 resize-none" placeholder="Brief description of this tag."></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <input wire:model="tagIsActive" type="checkbox" id="tagActive" class="rounded border-outline-variant/30 text-primary focus:ring-primary">
                    <label for="tagActive" class="text-sm text-on-surface font-medium">Active (Visible on frontend)</label>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-6 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 transition-colors">Cancel</button>
                    <button type="submit" class="bg-primary text-on-primary px-8 py-2.5 rounded-xl font-bold uppercase tracking-widest text-[10px] shadow-lg shadow-primary/20">
                        {{ $tagId ? 'Update' : 'Create' }} Tag
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
