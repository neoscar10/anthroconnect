<a href="{{ route('encyclopedia.theories.show', $theory->slug) }}" class="group flex gap-6 p-6 rounded-[28px] bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 shadow-sm hover:bg-orange-50/50 dark:hover:bg-orange-950/10 hover:border-primary/20 transition-all duration-300 cursor-pointer">
    <div class="h-14 w-14 shrink-0 flex items-center justify-center rounded-2xl bg-orange-100/50 dark:bg-orange-900/20 text-primary transform group-hover:scale-110 group-hover:rotate-6 transition-all shadow-sm">
        <span class="material-symbols-outlined text-3xl">hub</span>
    </div>
    <div class="space-y-2">
        <h4 class="text-xl font-headline font-bold text-stone-900 dark:text-stone-100 group-hover:text-primary transition-colors italic leading-tight">{{ $theory->title }}</h4>
        <p class="text-sm text-stone-500 dark:text-stone-400 line-clamp-2 leading-relaxed">
            {{ $theory->short_description ?: 'Analytical frameworks used to interpret social structures, cultural patterns, and human evolution.' }}
        </p>
        <div class="pt-2 flex items-center gap-2">
            <span class="text-[10px] font-extrabold text-stone-400 uppercase tracking-widest">Main Thinkers:</span>
            <span class="text-[10px] font-bold text-stone-600 dark:text-stone-300">{{ $theory->key_thinkers_text ?: 'Various Scholars' }}</span>
        </div>
    </div>
</a>
