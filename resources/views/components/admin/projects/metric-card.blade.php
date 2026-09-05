@props([
    'label',
    'value',
    'note',
    'icon',
    'tone' => 'blue',
])

<x-ui.metric-card
    class="project-metric-card"
    size="large"
    :label="$label"
    :value="$value"
    :icon="$icon"
    :tone="$tone"
    :note="$note"
/>
