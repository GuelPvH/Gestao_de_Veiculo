@props([
    'src',
    'alt' => '',
    'size' => 'md',
])

@php
    $sizeClass = match ($size) {
        'xs' => 'ui-avatar-xs',
        'sm' => 'ui-avatar-sm',
        'lg' => 'ui-avatar-lg',
        default => 'ui-avatar-md',
    };
@endphp

<img
    src="{{ $src }}"
    alt="{{ $alt }}"
    {{ $attributes->class(['ui-avatar', $sizeClass, 'rounded-circle']) }}
>
