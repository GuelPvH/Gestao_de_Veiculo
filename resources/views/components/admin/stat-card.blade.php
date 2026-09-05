@props([
    'label',
    'value',
    'icon',
    'tone' => 'blue',
    'note' => null,
])

<x-ui.metric-card
    class="dashboard-card"
    :label="$label"
    :value="$value"
    :icon="$icon"
    :tone="$tone"
    :note="$note"
/>
