@props([
    'label',
    'value',
    'icon',
    'tone' => 'blue',
    'note' => null,
    'size' => 'compact',
])

<article {{ $attributes->class(['card ui-metric-card ui-metric-card-'.$size, 'h-100']) }}>
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <span class="ui-metric-label">{{ $label }}</span>
            <span class="ui-metric-icon ui-tone-{{ $tone }}">
                <i class="bi {{ $icon }}" aria-hidden="true"></i>
            </span>
        </div>
        <div>
            <div class="ui-metric-value">{{ $value }}</div>
            @if ($note)
                <div class="ui-metric-note">{{ $note }}</div>
            @endif
        </div>
    </div>
</article>
