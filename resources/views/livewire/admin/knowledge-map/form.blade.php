<div class="p-8 max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('admin.knowledge-maps.index') }}" class="text-stone-400 hover:text-primary transition-colors flex items-center gap-2 text-xs font-bold uppercase tracking-widest mb-4">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Maps
        </a>
        <h2 class="text-3xl font-headline italic font-bold text-primary dark:text-stone-100">
            {{ $mapId ? 'Edit Knowledge Map' : 'Create Knowledge Map' }}
        </h2>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column: Basic Info -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-stone-900 p-8 rounded-2xl shadow-sm border border-stone-200/50">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">info</span>
                        General Information
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2 ml-1">Map Title</label>
                            <input wire:model="title" type="text" placeholder="e.g. Anthropology of Religion" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                            @error('title') <span class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2 ml-1">Subtitle</label>
                            <input wire:model="subtitle" type="text" placeholder="A brief hook for the learners" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                            @error('subtitle') <span class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2 ml-1">Description</label>
                            <textarea wire:model="description" rows="4" placeholder="Detailed overview of what this map covers..." class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20"></textarea>
                            @error('description') <span class="text-red-500 text-[10px] font-bold uppercase mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-stone-900 p-8 rounded-2xl shadow-sm border border-stone-200/50">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">settings_overscan</span>
                        Canvas Settings
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2 ml-1">Default Zoom</label>
                            <input wire:model="default_zoom" type="number" step="0.1" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2 ml-1">Background</label>
                            <select wire:model="canvas_background" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                                <option value="dotted">Dotted</option>
                                <option value="grid">Grid</option>
                                <option value="plain">Plain</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Media & Status -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-stone-900 p-8 rounded-2xl shadow-sm border border-stone-200/50">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">visibility</span>
                        Status & Visibility
                    </h3>

                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2 ml-1">Status</label>
                                <select wire:model="status" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 font-bold text-stone-700">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2 ml-1">Visibility</label>
                                <select wire:model="visibility" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 font-bold text-stone-700">
                                    <option value="public">Public</option>
                                    <option value="members_only">Members Only</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-6">
            <a href="{{ route('admin.knowledge-maps.index') }}" class="px-8 py-3 bg-stone-100 text-stone-600 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-stone-200 transition-all">
                Cancel
            </a>
            <button type="submit" class="px-10 py-3 bg-primary text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <span wire:loading wire:target="save" class="animate-spin text-sm">refresh</span>
                <span wire:loading.remove wire:target="save">
                    {{ $mapId ? 'Update Map Details' : 'Create Knowledge Map' }}
                </span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
