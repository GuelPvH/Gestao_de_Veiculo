@props([
    'label',
    'value',
    'note',
    'icon',
    'tone' => 'blue',
])

<article class="card project-metric-card h-100">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <span class="project-metric-label">{{ $label }}</span>
            <span class="project-metric-icon project-tone-{{ $tone }}" aria-hidden="true">
                <i class="bi {{ $icon }}"></i>
            </span>
        </div>
        <strong class="project-metric-value d-block">{{ $value }}</strong>
        <span class="project-metric-note d-block">{{ $note }}</span>
    </div>
</article>
