<div class="min-h-screen bg-stone-50">
    <!-- Hero Section -->
    <section class="relative bg-stone-900 py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-20 ethno-pattern"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 flex flex-col items-center text-center">
            <span class="text-orange-400 font-bold uppercase tracking-[0.3em] text-[10px] mb-6">Academic Curriculum</span>
            <h1 class="font-headline text-5xl md:text-7xl text-white italic mb-6">Anthropology Modules</h1>
            <p class="text-stone-400 text-lg md:text-xl max-w-2xl font-light leading-relaxed">
                Structured learning journeys through the complexities of human culture, evolution, and social structure.
            </p>
        </div>
    </section>

    <!-- Catalog Controls -->
    <div class="bg-white border-b border-stone-200 sticky top-16 z-30">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-6 overflow-x-auto no-scrollbar pb-1">
                <button wire:click="setTopic('')" 
                    class="whitespace-nowrap text-xs font-bold uppercase tracking-widest transition-colors {{ !$topicId ? 'text-primary' : 'text-stone-400 hover:text-stone-600' }}">
                    All Disciplines
                </button>
                @foreach($topics as $topic)
                    <button wire:click="setTopic({{ $topic->id }})" 
                        class="whitespace-nowrap text-xs font-bold uppercase tracking-widest transition-colors {{ $topicId == $topic->id ? 'text-primary' : 'text-stone-400 hover:text-stone-600' }}">
                        {{ $topic->name }}
                    </button>
                @endforeach
            </div>

            <div class="flex items-center gap-4 flex-1 lg:flex-none">
                <div class="relative flex-1 lg:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search modules..." 
                        class="w-full pl-10 pr-4 py-2 bg-stone-100 border-none rounded-xl text-xs focus:ring-1 focus:ring-primary">
                </div>
                
                <select wire:model.live="level" class="bg-stone-100 border-none rounded-xl text-xs font-bold uppercase tracking-wider px-4 py-2 focus:ring-1 focus:ring-primary appearance-none cursor-pointer">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Modules Grid -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        @if($modules->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                @foreach($modules as $module)
                    <div class="group bg-white rounded-3xl border border-stone-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden flex flex-col h-full">
                        <div class="aspect-[16/10] relative overflow-hidden bg-stone-100">
                            @if($module->cover_image)
                                <img src="{{ Storage::url($module->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center opacity-10">
                                    <span class="material-symbols-outlined text-5xl">school</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest text-primary shadow-sm">
                                    {{ $module->level ?? 'General' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-8 flex flex-col flex-1">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-orange-800">{{ $module->topic->name ?? 'Ethnography' }}</span>
                                <div class="h-1 w-1 rounded-full bg-stone-300"></div>
                                <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-stone-400">{{ $module->lessons_count }} Lessons</span>
                            </div>

                            <h3 class="font-headline text-2xl font-bold text-stone-900 italic mb-4 leading-tight group-hover:text-primary transition-colors">
                                {{ $module->title }}
                            </h3>

                            <p class="text-stone-600 text-sm line-clamp-3 mb-8 flex-1">
                                {{ $module->short_description }}
                            </p>

                            <div class="flex items-center justify-between pt-6 border-t border-stone-100 mt-auto">
                                <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-stone-400">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    <span>~{{ $module->estimated_duration ?? '45m' }}</span>
                                </div>
                                
                                <a href="{{ route('modules.show', $module->slug) }}" 
                                    class="text-xs font-bold uppercase tracking-widest text-primary flex items-center gap-2 group/btn">
                                    View Module
                                    <span class="material-symbols-outlined text-sm group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16">
                {{ $modules->links() }}
            </div>
        @else
            <div class="py-32 flex flex-col items-center text-center opacity-40">
                <span class="material-symbols-outlined text-6xl mb-6">menu_book</span>
                <h3 class="font-headline text-3xl italic">The catalog is quiet.</h3>
                <p class="text-sm uppercase tracking-widest mt-2">No modules match your current filters.</p>
                <button wire:click="$set('topicId', ''); $set('level', ''); $set('search', '')" class="mt-8 text-primary font-bold uppercase tracking-widest text-xs underline">Clear all filters</button>
            </div>
        @endif
    </section>
</div>
