<div x-data="{ open: @entangle('show') }" x-show="open" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 sm:p-6" x-on:keydown.escape.window="open = false">
    <!-- Backdrop -->
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-stone-900/60 backdrop-blur-md" @click="open = false"></div>

    <!-- Modal Content -->
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-12 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-12 sm:scale-95" class="relative bg-white rounded-[40px] shadow-2xl w-full max-w-2xl overflow-hidden border border-stone-200">
        <!-- Header -->
        <div class="relative bg-stone-900 p-6 sm:p-10 overflow-hidden">
            <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div class="space-y-2">
                    <span class="text-[9px] sm:text-[10px] font-bold text-primary uppercase tracking-widest leading-none">Scholarship Contribution</span>
                    <h2 class="text-2xl sm:text-3xl font-headline font-bold text-white italic leading-tight">Start a New Inquiry</h2>
                    <p class="text-stone-400 text-xs sm:text-sm max-w-md">Contribute your research questions or expertise.</p>
                </div>
                <button @click="open = false" class="text-stone-500 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>
        </div>

        <!-- Form -->
        <form wire:submit.prevent="save" class="p-6 sm:p-10 space-y-6 sm:space-y-8 bg-white max-h-[75vh] overflow-y-auto scrollbar-hide">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Topic Selection -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-3 text-left">Academic Domain</label>
                    <div class="relative">
                        <select wire:model="topic_id" class="w-full bg-stone-50 border-stone-100 rounded-2xl py-4 pl-4 pr-10 text-sm font-medium focus:ring-primary focus:border-primary appearance-none cursor-pointer">
                            <option value="">Select Domain...</option>
                            @foreach($topics as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-stone-300 pointer-events-none">expand_more</span>
                    </div>
                    @error('topic_id') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase tracking-tight text-left">{{ $message }}</span> @enderror
                </div>

                <!-- Tags -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-3 text-left">Keywords / Tags</label>
                    <input wire:model="tags" type="text" placeholder="e.g. Kinship, Fieldwork" class="w-full bg-stone-50 border-stone-100 rounded-2xl py-4 px-4 text-sm font-medium focus:ring-primary focus:border-primary">
                    <p class="text-[9px] text-stone-400 mt-1 italic text-left">Separate tags with commas.</p>
                </div>

                <!-- Title -->
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-3 text-left">Inquiry Title</label>
                    <input wire:model="title" type="text" placeholder="What is the central theme of your discussion?" class="w-full bg-stone-50 border-stone-100 rounded-2xl py-4 px-4 text-lg font-headline font-bold text-stone-900 focus:ring-primary focus:border-primary placeholder:text-stone-300">
                    @error('title') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase tracking-tight text-left">{{ $message }}</span> @enderror
                </div>

                <!-- Body -->
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-3 text-left">Narrative / Context</label>
                    <textarea wire:model="body" rows="6" placeholder="Provide background information, your hypothesis, or specific questions for the community..." class="w-full bg-stone-50 border-stone-100 rounded-[32px] p-6 text-sm leading-relaxed text-stone-700 focus:ring-primary focus:border-primary placeholder:text-stone-300"></textarea>
                    @error('body') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase tracking-tight text-left">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-6 border-t border-stone-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-xl">shield_person</span>
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] font-bold text-stone-900 uppercase tracking-tight leading-none">Verified Scholar</p>
                        <p class="text-[9px] text-stone-500 italic mt-1 leading-none">Contributing as {{ Auth::user()?->name ?? 'Guest Scholar' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <button type="button" @click="open = false" class="flex-1 sm:flex-none px-6 py-4 text-xs font-bold uppercase tracking-widest text-stone-400 hover:text-stone-900 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 sm:flex-none bg-primary hover:bg-orange-800 text-white font-bold py-4 px-10 rounded-2xl shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove>Publish Inquiry</span>
                        <span wire:loading class="animate-spin material-symbols-outlined">progress_activity</span>
                        <span class="material-symbols-outlined" wire:loading.remove>send</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
