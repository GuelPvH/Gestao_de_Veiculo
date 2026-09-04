@props([
    'title',
    'subtitle' => null,
])

<header {{ $attributes->class(['page-heading']) }}>
    <div>
        <h1>{{ $title }}</h1>
        @if ($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="page-heading-actions">
            {{ $actions }}
        </div>
    @endisset
</header>
