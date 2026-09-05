@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'tone' => 'blue',
])

<section {{ $attributes->class(['card ui-panel']) }}>
    @if ($title || isset($header))
        <header class="ui-panel-header">
            @if ($icon)
                <span class="ui-panel-icon ui-tone-{{ $tone }}">
                    <i class="bi {{ $icon }}" aria-hidden="true"></i>
                </span>
            @endif

            <div class="ui-panel-heading">
                @isset($header)
                    {{ $header }}
                @else
                    <h2>{{ $title }}</h2>
                    @if ($subtitle)
                        <p>{{ $subtitle }}</p>
                    @endif
                @endisset
            </div>

            @isset($actions)
                <div class="ui-panel-actions">
                    {{ $actions }}
                </div>
            @endisset
        </header>
    @endif

    <div class="ui-panel-body">
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="ui-panel-footer">
            {{ $footer }}
        </footer>
    @endisset
</section>
