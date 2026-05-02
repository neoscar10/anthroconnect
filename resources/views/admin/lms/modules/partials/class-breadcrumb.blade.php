<nav class="flex items-center gap-3 mb-8">
    <button wire:click="closeClass" class="text-stone-400 hover:text-primary transition-colors flex items-center gap-2">
        <span class="material-symbols-outlined text-[16px]">folder</span>
        <span class="text-[10px] font-bold uppercase tracking-widest">Classes</span>
    </button>
    <span class="material-symbols-outlined text-[14px] text-stone-300">chevron_right</span>
    <div class="flex items-center gap-2 bg-primary/10 text-primary px-4 py-2 rounded-xl">
        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
        <span class="text-[10px] font-bold uppercase tracking-widest">{{ $this->module->classes->find($selectedClassId)->title }}</span>
    </div>
</nav>
