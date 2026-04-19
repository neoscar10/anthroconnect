<div class="p-6 rounded-[24px] bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 shadow-sm hover:border-primary/40 hover:shadow-xl hover:-translate-y-1 transition-all group cursor-pointer flex flex-col h-full">
    <div class="space-y-4 flex-1">
        <div class="flex items-center justify-between">
            <h4 class="font-headline font-bold text-lg text-primary italic leading-tight group-hover:underline">{{ $concept->title }}</h4>
            <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors">psychology</span>
        </div>
        <p class="text-xs text-stone-500 dark:text-stone-400 line-clamp-4 leading-relaxed font-medium">
            {{ $concept->short_description ?: 'Essential terminology and conceptual foundations in anthropological theory and ethnographic practice.' }}
        </p>
    </div>
    <div class="mt-4 flex items-center text-[10px] uppercase font-extrabold tracking-widest text-stone-400 group-hover:text-primary transition-colors">
        Read Foundation <span class="material-symbols-outlined text-xs ml-1">trending_flat</span>
    </div>
</div>
