@props([
    'title',
    'value' => null,
    'meta' => null,
    'icon' => null,
    'tone' => 'blue',
])

<div {{ $attributes->class(['ui-detail-row']) }}>
    @if ($icon)
        <span class="ui-detail-icon ui-tone-{{ $tone }}">
            <i class="bi {{ $icon }}" aria-hidden="true"></i>
        </span>
    @endif

    <div class="ui-detail-copy">
        <span class="ui-detail-title">{{ $title }}</span>
        @if ($value)
            <strong class="ui-detail-value">{{ $value }}</strong>
        @endif
        @if ($meta)
            <span class="ui-detail-meta">{{ $meta }}</span>
        @endif
        {{ $slot }}
    </div>
</div>
