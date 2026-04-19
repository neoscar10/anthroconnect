@extends('layouts.public')

@section('content')
<main>
    <!-- Article Hero Header -->
    <header class="relative w-full h-[70vh] min-h-[500px] flex items-end">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-t from-stone-950/90 via-stone-950/40 to-transparent z-10"></div>
            @if($article->featured_image)
                <img alt="{{ $article->title }}" class="w-full h-full object-cover" src="{{ Storage::url($article->featured_image) }}"/>
            @else
                <div class="w-full h-full bg-stone-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-6xl text-stone-600">image</span>
                </div>
            @endif
        </div>
        <div class="relative z-20 max-w-4xl mx-auto px-4 pb-16 w-full text-white">
            <div class="flex items-center gap-2 mb-4">
                @if($article->topic)
                    <span class="bg-primary px-3 py-1 rounded text-xs font-bold uppercase tracking-widest">{{ $article->topic->name }}</span>
                @endif
                <span class="text-stone-300 text-sm">• {{ $article->reading_time_minutes ?? '5+' }} min read</span>
            </div>
            <h1 class="font-headline text-4xl md:text-6xl font-bold mb-6 leading-tight">{{ $article->title }}</h1>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary/20 border border-white/20 overflow-hidden">
                    <img alt="{{ $article->creator->name }}" class="w-full h-full object-cover" src="{{ $article->creator->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($article->creator->name) }}"/>
                </div>
                <div>
                    <p class="font-semibold">{{ $article->creator->name ?? 'AnthroConnect Editorial' }}</p>
                    <p class="text-sm text-stone-300">{{ $article->published_at ? $article->published_at->format('F d, Y') : 'Recently' }}</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <article class="max-w-2xl mx-auto px-6 py-20">
        <div class="prose-content text-lg text-stone-700 dark:text-stone-300 space-y-8 leading-relaxed">
            {!! $article->rendered_content_html !!}
        </div>

        @if($article->topic)
            <div class="mt-20 pt-10 border-t border-stone-200 dark:border-stone-800">
                <h4 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-4">Related Concepts</h4>
                <div class="flex flex-wrap gap-2">
                    <a class="px-4 py-1.5 bg-stone-100 dark:bg-stone-800 rounded-full text-sm hover:bg-primary hover:text-white transition-colors" href="{{ route('explore.index', ['topic_id' => $article->topic_id]) }}">
                        {{ $article->topic->name }}
                    </a>
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
                    <div class="aspect-video rounded-xl overflow-hidden mb-6 bg-stone-100 shadow-sm border border-stone-200/50">
                        @if($related->featured_image)
                            <img alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ Storage::url($related->featured_image) }}"/>
                        @else
                            <div class="w-full h-full flex items-center justify-center opacity-10">
                                <span class="material-symbols-outlined text-4xl">image</span>
                            </div>
                        @endif
                    </div>
                    @if($related->topic)
                        <span class="text-[10px] font-bold uppercase tracking-widest text-primary mb-2 block">{{ $related->topic->name }}</span>
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
            <a class="inline-block bg-white text-primary px-8 py-4 rounded-xl font-bold shadow-xl shadow-primary/20 hover:-translate-y-1 transition-all" href="#">View All Lessons</a>
        </div>
    </section>
</main>

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
@endsection
