@props([
    'tone' => 'neutral',
    'pill' => true,
])

@php
    $toneClass = match ($tone) {
        'primary', 'blue' => 'ui-badge-primary',
        'success', 'green' => 'ui-badge-success',
        'warning', 'yellow' => 'ui-badge-warning',
        'danger', 'red' => 'ui-badge-danger',
        'purple' => 'ui-badge-purple',
        default => 'ui-badge-neutral',
    };
@endphp

<span {{ $attributes->class(['ui-badge', $toneClass, 'ui-badge-pill' => $pill]) }}>
    {{ $slot }}
</span>
