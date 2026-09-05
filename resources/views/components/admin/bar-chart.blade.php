@php
    $months = [
        ['name' => 'Jan', 'height' => 43],
        ['name' => 'Fev', 'height' => 58],
        ['name' => 'Mar', 'height' => 67],
        ['name' => 'Abr', 'height' => 53],
        ['name' => 'Mai', 'height' => 77],
        ['name' => 'Jun', 'height' => 86],
    ];
@endphp

<article class="card dashboard-card chart-card h-100">
    <div class="card-body p-4 pb-3">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <h2 class="section-title mb-0">Leads por Mês</h2>
                <p class="text-secondary mb-0" style="font-size: 10px">Últimos 6 meses</p>
            </div>
            <span class="d-flex align-items-center gap-1 text-secondary" style="font-size: 10px">
                <span class="status-dot bg-primary"></span> Leads
            </span>
        </div>
        <div class="chart-area position-relative">
            <div class="chart-grid" aria-hidden="true"></div>
            <div class="chart-bars" role="img" aria-label="Gráfico de leads mensais, com crescimento de janeiro a junho">
                @foreach ($months as $month)
                    <div class="chart-column">
                        <div class="chart-bar" style="height: {{ $month['height'] }}%" title="{{ $month['height'] }}%"></div>
                        <span class="chart-month">{{ $month['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</article>
