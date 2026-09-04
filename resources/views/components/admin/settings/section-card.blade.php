@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'tone' => 'blue',
])

<x-ui.panel
    :title="$title"
    :subtitle="$subtitle"
    :icon="$icon"
    :tone="$tone"
    {{ $attributes->class(['settings-card']) }}
>
    {{ $slot }}
</x-ui.panel>
