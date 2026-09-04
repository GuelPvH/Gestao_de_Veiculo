@props([
    'title',
    'count',
    'tone' => 'slate',
])

<section class="project-kanban-column project-column-{{ $tone }}" aria-labelledby="project-column-{{ $tone }}">
    <header class="project-column-header d-flex align-items-center justify-content-between gap-3">
        <h2 id="project-column-{{ $tone }}" class="d-flex align-items-center gap-2 mb-0">
            <span class="project-column-dot" aria-hidden="true"></span>
            {{ $title }}
        </h2>
        <span class="project-column-count">{{ $count }}</span>
    </header>

    <div class="project-column-cards">
        {{ $slot }}
    </div>

    <button type="button" class="btn project-add-card w-100">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
        Adicionar card
    </button>
</section>
