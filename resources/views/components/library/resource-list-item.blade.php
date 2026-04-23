@props([
    'resource',
    'access' => ['allowed' => true, 'reason' => 'public', 'lock_message' => null, 'cta_label' => 'Open Resource', 'is_member_only' => false],
])

@php
    $href = $access['allowed'] ? route('library.show', $resource) : '#';
    $icon = match(optional($resource->resourceType)->slug) {
        'book' => 'mdi-book-open-page-variant',
        'report' => 'mdi-file-document-outline',
        'journal-article' => 'mdi-file-pdf-box',
        default => 'mdi-file-document-outline',
    };
@endphp

<article
    class="ac-list-resource {{ !$access['allowed'] ? 'is-locked js-library-locked' : '' }}"
    data-reason="{{ $access['reason'] }}"
    data-message="{{ $access['lock_message'] }}"
>
    <a href="{{ $href }}" class="ac-list-cover">
        <img src="{{ $resource->cover_url }}" alt="{{ $resource->title }}">
        @if(!$access['allowed'])
            <div class="ac-list-lock-overlay">
                <span class="material-symbols-outlined text-sm text-white">lock</span>
            </div>
        @endif
    </a>

    <div class="ac-list-body">
        <div class="ac-list-title-row">
            <h3><a href="{{ $href }}">{{ $resource->title }}</a></h3>

            @if(!$access['allowed'])
                <span class="material-symbols-outlined text-sm text-stone-400">lock</span>
            @else
                <span class="material-symbols-outlined text-sm text-stone-300">bookmark</span>
            @endif
        </div>

        <p>
            {{ $resource->author_display ?: 'Unknown Author' }}
            @if($resource->publication_year)
                • {{ $resource->publication_year }}
            @endif
        </p>

        <div class="ac-list-tags">
            @if($resource->region)
                <span>{{ $resource->region->name }}</span>
            @endif

            @foreach($resource->topics->take(2) as $topic)
                <span>{{ $topic->name }}</span>
            @endforeach
        </div>
    </div>
</article>
