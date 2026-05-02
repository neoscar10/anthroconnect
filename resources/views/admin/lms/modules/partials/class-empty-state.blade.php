<div class="py-20 flex flex-col items-center justify-center text-center">
    <div class="w-24 h-24 rounded-[32px] bg-surface-container-low flex items-center justify-center text-stone-200 mb-6">
        <span class="material-symbols-outlined text-5xl">folder_off</span>
    </div>
    <h3 class="font-headline text-2xl font-bold italic text-on-surface mb-2">No class folders yet</h3>
    <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 max-w-sm">Create your first class folder before adding videos and reading materials to this module.</p>
    
    <button wire:click="openClassModal" class="mt-8 bg-secondary text-on-secondary px-8 py-3 rounded-2xl font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-secondary/20 hover:-translate-y-0.5 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add</span>
        Create First Class
    </button>
</div>
