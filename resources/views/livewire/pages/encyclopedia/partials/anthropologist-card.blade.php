<div class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-[32px] overflow-hidden hover:shadow-2xl hover:shadow-primary/5 transition-all duration-500 group flex flex-col h-full">
    <div class="p-8 space-y-6 flex-1">
        <div class="flex items-center gap-6">
            <div class="h-28 w-28 rounded-3xl overflow-hidden bg-orange-100 dark:bg-orange-950/30 shrink-0 border border-orange-200/50 dark:border-orange-800/30 grayscale group-hover:grayscale-0 transition-all duration-700 transform group-hover:scale-105 shadow-md">
                @if($person->profile_image)
                    <img alt="{{ $person->full_name }}" class="object-cover h-full w-full" src="{{ Storage::url($person->profile_image) }}"/>
                @else
                    <div class="h-full w-full flex items-center justify-center opacity-20">
                        <span class="material-symbols-outlined text-4xl">person</span>
                    </div>
                @endif
            </div>
            <div class="space-y-2 flex-1">
                <span class="text-[9px] font-extrabold text-primary uppercase tracking-[0.2em] px-3 py-1 bg-primary/5 rounded-full block w-fit">
                    {{ $person->topics->first()?->name ?? ($person->discipline_or_specialization ?: 'Anthropologist') }}
                </span>
                <h3 class="text-2xl font-headline font-bold text-stone-900 dark:text-stone-100 leading-tight group-hover:text-primary transition-colors italic">
                    {{ $person->full_name }}
                </h3>
            </div>
        </div>
        
        <div class="space-y-4">
            <p class="text-sm text-stone-500 dark:text-stone-400 line-clamp-3 leading-relaxed font-medium">
                {{ $person->summary ?: 'Legacy thinkers who paved the way for modern anthropological inquiry and cultural understanding.' }}
            </p>
            
            <div class="flex flex-wrap gap-2 pt-2 border-t border-stone-100 dark:border-stone-800/50">
                <span class="text-[10px] text-stone-400 italic flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">public</span>
                    {{ $person->nationality ?: 'Global' }}
                </span>
                @if($person->birth_year)
                    <span class="text-[10px] text-stone-400 italic flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">history_toggle_off</span>
                        {{ $person->birth_year }} – {{ $person->death_year ?: 'Present' }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="px-8 pb-8">
        <button class="w-full py-4 bg-stone-50 dark:bg-stone-950 hover:bg-primary hover:text-white transition-all rounded-2xl text-[10px] uppercase tracking-widest font-extrabold text-primary shadow-sm hover:shadow-lg hover:shadow-primary/30 transform active:scale-95">
            View Research Profile
        </button>
    </div>
</div>
