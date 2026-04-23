<div>
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline text-4xl font-bold italic text-on-surface">Community Topics</h2>
            <p class="text-on-surface-variant font-body mt-1">Classify scholarship and discussion domains.</p>
        </div>
        <button wire:click="openModal()" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold uppercase tracking-wider shadow-md hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span>
            New Topic
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant/10 mb-8">
        <div class="relative max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search topics..." class="w-full bg-surface-container-low border-none rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/20">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold">Topic Name</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold text-center">Icon</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold text-center">Order</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold">Status</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[10px] font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                @foreach($topics as $topic)
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-8 rounded-full" style="background-color: {{ $topic->color ?? '#9a3412' }}"></div>
                                <div>
                                    <p class="font-bold text-stone-900">{{ $topic->name }}</p>
                                    <p class="text-[10px] text-stone-400 font-medium tracking-wider">{{ $topic->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="material-symbols-outlined text-stone-500">{{ $topic->icon ?? 'category' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-stone-600 text-sm">
                            {{ $topic->sort_order }}
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleActive({{ $topic->id }})" 
                                class="px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest {{ $topic->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $topic->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="openModal({{ $topic->id }})" class="p-2 text-stone-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-xl">edit</span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $topics->links() }}
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-stone-900/50 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
        
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50">
                <h3 class="font-headline text-xl font-bold italic">{{ $editingTopic ? 'Edit Topic' : 'Add New Topic' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-stone-400 hover:text-stone-900">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form wire:submit.prevent="save" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-1">Topic Name</label>
                        <input wire:model.live="name" type="text" class="w-full bg-stone-50 border-stone-200 rounded-lg text-sm focus:ring-primary">
                        @error('name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-1">Slug</label>
                        <input wire:model="slug" type="text" class="w-full bg-stone-50 border-stone-200 rounded-lg text-sm focus:ring-primary">
                        @error('slug') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-1">Icon (Material Symbol)</label>
                        <input wire:model="icon" type="text" placeholder="e.g. groups" class="w-full bg-stone-50 border-stone-200 rounded-lg text-sm focus:ring-primary">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-1">Color (Hex)</label>
                        <input wire:model="color" type="color" class="w-full h-10 bg-stone-50 border-stone-200 rounded-lg focus:ring-primary">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full bg-stone-50 border-stone-200 rounded-lg text-sm focus:ring-primary"></textarea>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-1">Sort Order</label>
                        <input wire:model="sort_order" type="number" class="w-full bg-stone-50 border-stone-200 rounded-lg text-sm focus:ring-primary">
                    </div>

                    <div class="col-span-1 flex items-center pt-6">
                        <label class="flex items-center cursor-pointer">
                            <input wire:model="is_active" type="checkbox" class="rounded border-stone-300 text-primary focus:ring-primary mr-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-stone-500">Active Status</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2 text-xs font-bold uppercase tracking-widest text-stone-400 hover:text-stone-600 transition-colors">Cancel</button>
                    <button type="submit" class="px-8 py-2 bg-primary text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                        {{ $editingTopic ? 'Update Topic' : 'Create Topic' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
