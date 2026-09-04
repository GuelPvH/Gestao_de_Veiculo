@props([
    'title',
    'company',
    'type',
    'owner',
    'start',
    'value',
    'technologies' => [],
    'avatars' => [],
    'priority' => null,
    'critical' => false,
    'completed' => false,
    'end' => null,
    'progress' => 0,
    'accent' => 'slate',
    'typeTone' => 'blue',
])

<article class="card project-card project-accent-{{ $accent }}">
    <div class="project-card-header d-flex align-items-start justify-content-between gap-3">
        <div class="min-w-0">
            <div class="project-card-badges d-flex flex-wrap align-items-center gap-1">
                @if ($completed)
                    <span class="project-state-badge project-state-completed"><i class="bi bi-check-lg" aria-hidden="true"></i>Concluído</span>
                @elseif ($priority)
                    <span class="project-priority-badge project-priority-{{ strtolower($priority) }}">{{ $priority }}</span>
                @endif
                @if ($critical)
                    <span class="project-state-badge project-state-critical"><i class="bi bi-exclamation-diamond-fill" aria-hidden="true"></i>Prazo Crítico</span>
                @endif
            </div>
            <h3>{{ $title }}</h3>
            <p>{{ $company }}</p>
        </div>
        <button type="button" class="btn project-menu-button" aria-label="Mais opções para {{ $title }}">
            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
        </button>
    </div>

    <div class="project-card-body">
        <span class="project-type project-type-{{ $typeTone }}">{{ $type }}</span>

        <div class="project-meta-item">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span>{{ $owner }}</span>
        </div>

        <div class="project-date-grid">
            <div class="project-meta-item {{ $completed ? 'text-success' : '' }}">
                <i class="bi {{ $completed ? 'bi-check-circle-fill' : 'bi-calendar-fill' }}" aria-hidden="true"></i>
                <span>{{ $start }}</span>
            </div>
            @if ($end)
                <div class="project-meta-item {{ $critical ? 'text-danger' : '' }}">
                    <i class="bi {{ $critical ? 'bi-flag-fill' : 'bi-clock-fill' }}" aria-hidden="true"></i>
                    <span>{{ $end }}</span>
                </div>
            @endif
        </div>

        <strong class="project-value d-block">{{ $value }}</strong>

        <div class="project-stack d-flex flex-wrap gap-1" aria-label="Tecnologias">
            @foreach ($technologies as $technology)
                <span>{{ $technology }}</span>
            @endforeach
        </div>

        <div class="project-team d-flex align-items-center" aria-label="Equipe do projeto">
            @foreach ($avatars as $avatar)
                <img src="{{ asset('images/admin/projects/avatar-'.$avatar.'.jpg') }}" alt="Integrante da equipe de {{ $title }}">
            @endforeach
        </div>

        <div class="project-progress">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <span>Progresso</span>
                <strong>{{ $progress }}%</strong>
            </div>
            <div class="progress" role="progressbar" aria-label="Progresso de {{ $title }}" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>
</article>
