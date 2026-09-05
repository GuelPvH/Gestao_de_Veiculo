<x-admin.layout title="Dashboard" page="Dashboard">
    <h1 class="visually-hidden">Dashboard administrativo</h1>

    <section class="row g-3 mb-3" aria-label="Indicadores gerais">
        <div class="col-12 col-sm-6 col-xl-3">
            <x-admin.stat-card label="Leads este mês" value="28" icon="bi-person-plus-fill" tone="blue" note="+12% este mês" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-admin.stat-card label="Projetos Ativos" value="7" icon="bi-file-earmark-text-fill" tone="purple" note="em andamento" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-admin.stat-card label="Receita Total" value="R$ 142.500" icon="bi-check-circle-fill" tone="green" note="este mês" />
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <x-admin.stat-card label="Taxa de Conversão" value="32%" icon="bi-hourglass-split" tone="yellow" note="leads → projetos" />
        </div>
    </section>

    <section class="row g-3 mb-3" aria-label="Gráficos do dashboard">
        <div class="col-12 col-xl-9">
            <x-admin.bar-chart />
        </div>
        <div class="col-12 col-xl-3">
            <x-admin.status-chart />
        </div>
    </section>

    <section class="row g-3" aria-label="Atividades e leads recentes">
        <div class="col-12 col-xxl-6">
            <x-admin.activity-feed />
        </div>
        <div class="col-12 col-xxl-6">
            <x-admin.leads-table />
        </div>
    </section>
</x-admin.layout>
