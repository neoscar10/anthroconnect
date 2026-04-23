@props(['label', 'value'])

@if(!blank($value))
    <div class="ac-meta-row">
        <span>{{ $label }}</span>
        <strong>{{ $value }}</strong>
    </div>
@endif
