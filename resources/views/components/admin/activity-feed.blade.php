@php
    $activities = [
        ['icon' => 'bi-person-plus-fill', 'tone' => 'blue', 'title' => 'Lead recebido — João Silva', 'detail' => 'Formulário do site — 15 Jun 2025, 09:42'],
        ['icon' => 'bi-file-earmark-text-fill', 'tone' => 'purple', 'title' => 'Proposta enviada — Carlos Mendes', 'detail' => 'Dashboard BI — 14 Jun 2025, 14:20'],
        ['icon' => 'bi-telephone-fill', 'tone' => 'green', 'title' => 'Projeto iniciado — Ana Lima', 'detail' => 'Landing Page Varejo — 13 Jun 2025, 10:05'],
        ['icon' => 'bi-eye-fill', 'tone' => 'yellow', 'title' => 'Lead visualizado — Maria Santos', 'detail' => 'Admin User — 13 Jun 2025, 11:00'],
        ['icon' => 'bi-calendar-check-fill', 'tone' => 'blue', 'title' => 'Reunião agendada — Rafael Moura', 'detail' => 'EduPlatz — 12 Jun 2025, 16:00'],
        ['icon' => 'bi-x-lg', 'tone' => 'danger', 'title' => 'Lead perdido — Camila Nunes', 'detail' => 'AgriTech — 11 Jun 2025, 09:15'],
    ];
@endphp

<article class="card dashboard-card activity-card h-100">
    <div class="card-header d-flex align-items-center justify-content-between bg-white px-4 py-3 border-bottom">
        <h2 class="section-title mb-0">Atividade Recente</h2>
        <a href="#" class="text-decoration-none" style="font-size: 10px">Ver tudo</a>
    </div>
    <div class="card-body d-grid gap-3 px-4 py-3">
        @foreach ($activities as $activity)
            <div class="d-flex gap-3 position-relative">
                <span class="activity-icon metric-icon-{{ $activity['tone'] === 'danger' ? 'yellow' : $activity['tone'] }} {{ $activity['tone'] === 'danger' ? 'bg-danger-subtle text-danger' : '' }}">
                    <i class="bi {{ $activity['icon'] }}" aria-hidden="true"></i>
                </span>
                @unless ($loop->last)
                    <span class="activity-line" aria-hidden="true"></span>
                @endunless
                <div class="activity-copy lh-sm pt-1">
                    <strong class="d-block fw-semibold">{{ $activity['title'] }}</strong>
                    <small>{{ $activity['detail'] }}</small>
                </div>
            </div>
        @endforeach
    </div>
</article>
