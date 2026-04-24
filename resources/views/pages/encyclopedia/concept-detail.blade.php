@extends('layouts.public')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-8 md:py-16">
    <!-- Hero Header -->
    <section class="bg-white dark:bg-stone-900 rounded-[40px] shadow-sm border border-primary/10 overflow-hidden mb-16">
        <div class="flex flex-col md:flex-row items-center gap-12 p-8 md:p-16">
            <div class="w-full md:w-1/3 shrink-0 rounded-3xl overflow-hidden aspect-video md:aspect-square bg-stone-100 dark:bg-stone-800 flex items-center justify-center relative group">
                @if($concept->featured_image)
                    <img src="{{ Storage::url($concept->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                @else
                    <span class="material-symbols-outlined text-[120px] text-primary/20 group-hover:scale-110 transition-transform duration-700">concept_box</span>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-primary/10 to-transparent"></div>
            </div>
            <div class="flex-1 space-y-6 text-center md:text-left">
                <div>
                    <span class="text-primary font-extrabold tracking-widest text-[10px] uppercase bg-primary/5 px-3 py-1 rounded-full">Core Anthropological Concept</span>
                    <h2 class="text-4xl md:text-6xl font-headline font-bold mt-4 text-stone-900 dark:text-stone-100 italic leading-tight">
                        {{ $concept->title }}
                    </h2>
                </div>
                <p class="text-xl text-stone-600 dark:text-stone-300 leading-relaxed max-w-2xl font-medium">
                    {{ $concept->short_description }}
                </p>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-20">
            
            <!-- Conceptual Breakdown -->
            <section>
                <div class="flex items-center gap-4 mb-10 border-b border-stone-100 dark:border-stone-800 pb-6">
                    <span class="material-symbols-outlined text-primary text-3xl">lightbulb</span>
                    <h3 class="text-3xl font-headline font-bold italic text-stone-900 dark:text-stone-100">Conceptual Breakdown</h3>
                </div>
                <div class="prose-content text-lg text-stone-700 dark:text-stone-300 leading-relaxed font-serif-body">
                    {!! \Illuminate\Support\Str::markdown($concept->body_markdown ?? '') !!}
                </div>
            </section>

            <!-- Key Figures -->
            @if($concept->anthropologists->isNotEmpty())
            <section>
                <div class="flex items-center gap-4 mb-10 border-b border-stone-100 dark:border-stone-800 pb-6">
                    <span class="material-symbols-outlined text-primary text-3xl">person_search</span>
                    <h3 class="text-3xl font-headline font-bold italic text-stone-900 dark:text-stone-100">Associated Scholars</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($concept->anthropologists as $thinker)
                    <a href="{{ route('encyclopedia.anthropologists.show', $thinker->slug) }}" class="flex items-center gap-4 p-6 rounded-2xl bg-white dark:bg-stone-900 border border-stone-100 dark:border-stone-800 hover:border-primary/30 hover:shadow-lg transition-all group">
                        <div class="w-16 h-16 rounded-full bg-primary/5 overflow-hidden shrink-0 border border-stone-100 dark:border-stone-700">
                            @if($thinker->profile_image)
                                <img src="{{ Storage::url($thinker->profile_image) }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all">
                            @else
                                <div class="w-full h-full flex items-center justify-center opacity-20">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-stone-900 dark:text-stone-100 group-hover:text-primary transition-colors">{{ $thinker->full_name }}</h4>
                            <p class="text-[10px] text-stone-500 font-bold uppercase tracking-widest">{{ $thinker->discipline_or_specialization ?: 'Anthropologist' }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-16">
            
            <!-- Quick Context -->
            <section class="p-8 bg-stone-50 dark:bg-stone-900 rounded-[32px] border border-stone-200 dark:border-stone-800">
                <h3 class="font-bold text-[10px] uppercase tracking-[0.2em] mb-8 text-stone-400">Contextual Info</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white dark:bg-stone-800 flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-primary text-xl">database</span>
                        </div>
                        <p class="text-sm font-bold text-stone-700 dark:text-stone-300">Foundational Concept</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white dark:bg-stone-800 flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-primary text-xl">share</span>
                        </div>
                        <p class="text-sm font-bold text-stone-700 dark:text-stone-300">Cross-Disciplinary</p>
                    </div>
                </div>
            </section>

            <!-- Related Concepts -->
            @if($relatedConcepts->isNotEmpty())
            <section>
                <h3 class="font-bold text-[10px] uppercase tracking-[0.2em] mb-8 text-stone-400">Related Concepts</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($relatedConcepts as $related)
                    <a href="{{ route('encyclopedia.concepts.show', $related->slug) }}" class="px-4 py-2 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl text-xs font-bold text-stone-600 dark:text-stone-400 hover:border-primary hover:text-primary transition-all shadow-sm">
                        {{ $related->title }}
                    </a>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Community CTA -->
            <section class="bg-stone-900 text-white p-10 rounded-[40px] shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                <div class="relative z-10">
                    <h3 class="font-headline font-bold text-2xl italic mb-4">Engage & Explore</h3>
                    <p class="text-sm text-stone-400 leading-relaxed mb-8">
                        How does {{ $concept->title }} apply to your current research? Share your perspectives in the community.
                    </p>
                    <a href="{{ route('community.index') }}" class="block w-full py-4 bg-primary hover:bg-orange-800 transition-colors rounded-xl text-center text-[10px] font-extrabold uppercase tracking-widest">
                        Join Discussion
                    </a>
                </div>
            </section>

        </div>
    </div>
</main>

<style>
    .font-serif-body { font-family: 'Lora', serif; }
    .prose-content p { margin-bottom: 2rem; }
    .prose-content h2, .prose-content h3 { font-family: 'Lora', serif; font-weight: 700; color: #9e5015; margin-top: 3rem; margin-bottom: 1.5rem; }
    .prose-content ul, .prose-content ol { margin-left: 2rem; margin-bottom: 2rem; }
    .prose-content ul { list-style: disc; }
    .prose-content ol { list-style: decimal; }
    .prose-content blockquote { border-left: 4px solid #9e5015; padding-left: 2rem; font-style: italic; margin: 3rem 0; color: #57534e; }
</style>
@endsection
