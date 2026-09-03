@props([
    'label',
    'value',
    'icon',
    'tone' => 'blue',
    'note' => null,
])

<article class="card dashboard-card metric-card h-100">
    <div class="card-body d-flex flex-column justify-content-between p-4">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <span class="metric-label">{{ $label }}</span>
            <span class="metric-icon metric-icon-{{ $tone }}">
                <i class="bi {{ $icon }}" aria-hidden="true"></i>
            </span>
        </div>
        <div>
            <div class="metric-value">{{ $value }}</div>
            @if ($note)
                <div class="metric-note mt-2">{!! $note !!}</div>
            @endif
        </div>
    </div>
</article>
