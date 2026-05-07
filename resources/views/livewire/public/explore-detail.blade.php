<main>
    <!-- Article Hero Header -->
    <header class="bg-stone-950 text-white overflow-hidden border-b border-stone-800">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 items-stretch min-h-[60vh]">
            <!-- Content Column -->
            <div class="px-6 py-16 lg:py-24 flex flex-col justify-center order-2 lg:order-1">
                <nav class="flex items-center gap-2 mb-8 text-[10px] uppercase font-bold tracking-[0.2em] text-stone-400">
                    <a wire:navigate href="{{ route('explore.index') }}" class="hover:text-primary transition-colors">Explore</a>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="text-stone-300">Narrative Archive</span>
                </nav>

                <div class="flex items-center gap-3 mb-6">
                    @if($article->tags->isNotEmpty())
                        <span class="bg-primary/20 text-primary border border-primary/30 px-3 py-1 rounded text-[10px] font-bold uppercase tracking-widest">{{ $article->tags->first()->name }}</span>
                    @endif
                    <span class="text-stone-400 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        {{ $article->reading_time_minutes ?? '5+' }} min read
                    </span>
                </div>

                <h1 class="font-headline italic text-4xl md:text-6xl font-bold mb-8 leading-tight text-white drop-shadow-sm">{{ $article->title }}</h1>
                
                <div class="flex items-center gap-4 pt-4 border-t border-stone-800/50 mt-4">
                    <div class="w-14 h-14 rounded-full bg-stone-800 border border-stone-700 overflow-hidden shrink-0">
                        <img alt="{{ $article->creator->name ?? 'Author' }}" class="w-full h-full object-cover" src="{{ $article->creator->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($article->creator->name ?? 'A') }}"/>
                    </div>
                    <div>
                        <p class="font-bold text-stone-100 text-lg">{{ $article->creator->name ?? 'AnthroConnect Editorial' }}</p>
                        <p class="text-xs text-stone-500 uppercase tracking-widest mt-0.5">{{ $article->published_at ? $article->published_at->format('F d, Y') : 'Recently' }}</p>
                    </div>
                </div>
            </div>

            <!-- Image Column -->
            <div class="relative min-h-[400px] lg:min-h-full order-1 lg:order-2">
                @if($article->featured_image)
                    <div class="absolute inset-0 bg-cover bg-center grayscale-[0.2] hover:grayscale-0 transition-all duration-700" 
                         style="background-image: url('{{ Storage::url($article->featured_image) }}')">
                        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-transparent to-transparent lg:block hidden"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-950 via-transparent to-transparent lg:hidden block"></div>
                    </div>
                @else
                    <div class="absolute inset-0 bg-stone-900 flex items-center justify-center">
                        <span class="material-symbols-outlined text-6xl text-stone-800">image</span>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <article class="max-w-2xl mx-auto px-6 py-20">
        <div class="prose-content text-lg text-stone-700 dark:text-stone-300 space-y-8 leading-relaxed">
            {!! $article->rendered_content_html !!}
        </div>

        @if($article->tags->isNotEmpty())
            <div class="mt-20 pt-10 border-t border-stone-200 dark:border-stone-800">
                <h4 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-4">Related Concepts</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                        <a class="px-4 py-1.5 bg-stone-100 dark:bg-stone-800 rounded-full text-sm hover:bg-primary hover:text-white transition-colors" href="{{ route('explore.index', ['tag_id' => $tag->id]) }}">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>

    <!-- Related Stories -->
    @if($relatedArticles->isNotEmpty())
    <section class="max-w-7xl mx-auto px-6 py-24 border-t border-stone-200 dark:border-stone-800">
        <h3 class="font-headline text-3xl font-bold mb-12 text-center text-stone-900 dark:text-stone-100">Related Stories</h3>
        <div class="grid md:grid-cols-2 gap-12">
            @foreach($relatedArticles as $related)
                <a href="{{ route('explore.show', $related->slug) }}" class="group block">
                    <div class="aspect-video rounded-xl overflow-hidden mb-6 bg-stone-100 shadow-sm border border-stone-200/50 relative">
                        @if($related->featured_image)
                            <img alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ Storage::url($related->featured_image) }}"/>
                        @else
                            <div class="w-full h-full flex items-center justify-center opacity-10">
                                <span class="material-symbols-outlined text-4xl">image</span>
                            </div>
                        @endif

                        @if($related->is_members_only && !auth()->user()?->isMember())
                            <div class="absolute inset-0 bg-stone-950/40 backdrop-blur-[2px] flex items-center justify-center text-white">
                                <span class="material-symbols-outlined text-4xl">lock</span>
                            </div>
                        @endif
                    </div>
                    @if($related->tags->isNotEmpty())
                        <span class="text-[10px] font-bold uppercase tracking-widest text-primary mb-2 block">{{ $related->tags->first()->name }}</span>
                    @endif
                    <h4 class="font-headline text-2xl font-bold mb-3 group-hover:text-primary transition-colors text-stone-900 dark:text-stone-100">{{ $related->title }}</h4>
                    <p class="text-stone-600 dark:text-stone-400 line-clamp-2 text-sm">{{ $related->excerpt }}</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Bottom CTA -->
    <section class="bg-primary py-20 px-6 text-white text-center">
        <div class="max-w-2xl mx-auto">
            <span class="material-symbols-outlined text-5xl mb-6">school</span>
            <h3 class="font-headline text-3xl font-bold mb-4">Learn the Anthropology Behind This Story</h3>
            <p class="text-white/80 mb-10 text-lg">Deepen your understanding with our interactive lesson modules on the history of human nutrition and social development.</p>
            <a class="inline-block bg-white text-primary px-8 py-4 rounded-xl font-bold shadow-xl shadow-primary/20 hover:-translate-y-1 transition-all" href="{{ route('modules.index') }}">View All Lessons</a>
        </div>
    </section>

    <style>
        /* Editorial Typography Refinements */
        .prose-content {
            font-family: 'Public Sans', sans-serif;
        }
        .prose-content h2 {
            font-family: 'Lora', serif;
            font-size: 1.875rem;
            font-weight: 700;
            color: #9a3412; /* primary */
            margin-top: 2.5rem;
            margin-bottom: 1.5rem;
        }
        .prose-content h3 {
            font-family: 'Lora', serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .prose-content p {
            margin-bottom: 1.5rem;
        }
        .prose-content blockquote {
            border-left: 4px solid #9a3412;
            padding-left: 2rem;
            font-style: italic;
            font-family: 'Lora', serif;
            font-size: 1.5rem;
            color: #44403c;
            margin: 3rem 0;
        }
        .prose-content ul, .prose-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
            list-style: disc;
        }
        .prose-content li {
            margin-bottom: 0.5rem;
        }
    </style>
</main>
