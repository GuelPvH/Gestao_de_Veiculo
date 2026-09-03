@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'tone' => 'blue',
])

<section {{ $attributes->class(['card settings-card']) }}>
    <header class="settings-card-header">
        @if ($icon)
            <span class="settings-card-icon settings-card-icon-{{ $tone }}">
                <i class="bi {{ $icon }}" aria-hidden="true"></i>
            </span>
        @endif
        <div>
            <h2>{{ $title }}</h2>
            @if ($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
        </div>
    </header>
    <div class="settings-card-body">
        {{ $slot }}
    </div>
</section>
