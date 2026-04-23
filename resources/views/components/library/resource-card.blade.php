@props([
    'resource',
    'access' => ['allowed' => true, 'reason' => 'public', 'lock_message' => null, 'cta_label' => 'Open Resource', 'is_member_only' => false],
])

@php
    $href = $access['allowed'] ? route('library.show', $resource) : '#';
@endphp

<article
    class="ac-resource-card {{ !$access['allowed'] ? 'is-locked js-library-locked' : '' }}"
    data-reason="{{ $access['reason'] }}"
    data-message="{{ $access['lock_message'] }}"
>
    <a href="{{ $href }}" class="ac-card-image">
        <img src="{{ $resource->cover_url }}" alt="{{ $resource->title }}">

        @if(!$access['allowed'])
            <span class="ac-lock-badge">
                <i class="mdi mdi-lock"></i> Member
            </span>
        @endif
    </a>

    <div class="ac-card-body">
        <div class="ac-card-meta">
            <span>{{ optional($resource->resourceType)->name ?: 'Resource' }}</span>
            @if($resource->publication_year)
                <em>{{ $resource->publication_year }}</em>
            @endif
        </div>

        <h3>
            <a href="{{ $href }}">{{ $resource->title }}</a>
        </h3>

        <p class="ac-card-author">By {{ $resource->author_display ?: 'Unknown Author' }}</p>
        <p class="ac-card-excerpt">{{ $resource->excerpt }}</p>

        <a href="{{ $href }}" class="ac-card-button">
            {{ $access['allowed'] ? 'Open Resource' : $access['cta_label'] }}
        </a>
    </div>
</article>
