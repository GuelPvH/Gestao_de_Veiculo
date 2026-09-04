@props([
    'value',
    'label',
    'tone' => 'primary',
    'showValue' => false,
])

@php($safeValue = max(0, min(100, (int) $value)))

<div class="ui-progress-group">
    @if ($showValue)
        <div class="ui-progress-copy">
            <span>{{ $label }}</span>
            <strong>{{ $safeValue }}%</strong>
        </div>
    @endif

    <div
        {{ $attributes->class(['progress ui-progress', 'ui-progress-'.$tone]) }}
        role="progressbar"
        aria-label="{{ $label }}"
        aria-valuenow="{{ $safeValue }}"
        aria-valuemin="0"
        aria-valuemax="100"
    >
        <div class="progress-bar" style="--progress-value: {{ $safeValue }}%"></div>
    </div>
</div>
