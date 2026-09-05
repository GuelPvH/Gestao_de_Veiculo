@php
    $columns = [
        [
            'title' => 'Em Análise',
            'tone' => 'slate',
            'projects' => [
                ['title' => 'Prontuário Eletrônico', 'company' => 'Saúde Tech', 'priority' => 'Alta', 'type' => 'Sistema Web', 'typeTone' => 'blue', 'owner' => 'Beatriz Rocha', 'start' => 'Início: 10 Jun', 'end' => '90 dias', 'value' => 'R$ 28.000', 'technologies' => ['React', 'Node', 'MongoDB'], 'avatars' => [1], 'progress' => 0, 'accent' => 'slate'],
                ['title' => 'Plataforma de Ensino EduPlat', 'company' => 'Rafael Moura', 'priority' => 'Média', 'type' => 'Software Custom', 'typeTone' => 'purple', 'owner' => 'Rafael Moura', 'start' => 'Início: 9 Jun', 'end' => '120 dias', 'value' => 'R$ 45.000', 'technologies' => ['Next.js', 'PostgreSQL'], 'avatars' => [3], 'progress' => 0, 'accent' => 'slate'],
            ],
        ],
        [
            'title' => 'Em Andamento',
            'tone' => 'blue',
            'projects' => [
                ['title' => 'Sistema ERP Industrial', 'company' => 'Ind. Moderna', 'priority' => 'Alta', 'critical' => true, 'type' => 'Sistemas Web', 'typeTone' => 'blue', 'owner' => 'Pedro Costa', 'start' => '1 Abr', 'end' => '15 Jul', 'value' => 'R$ 85.000', 'technologies' => ['React', 'Node'], 'avatars' => [4, 8], 'progress' => 78, 'accent' => 'blue'],
                ['title' => 'Plataforma CRM Comercial', 'company' => 'Logística SA', 'priority' => 'Alta', 'type' => 'Software Custom', 'typeTone' => 'purple', 'owner' => 'Maria Santos', 'start' => '15 Mar', 'end' => 'Em andamento', 'value' => 'R$ 62.000', 'technologies' => ['Vue.js', 'Laravel'], 'avatars' => [5, 9], 'progress' => 45, 'accent' => 'indigo'],
                ['title' => 'API Gateway Financeiro', 'company' => 'TechBR', 'priority' => 'Média', 'type' => 'APIs', 'typeTone' => 'orange', 'owner' => 'João Silva', 'start' => '1 Mai', 'end' => 'Em andamento', 'value' => 'R$ 38.000', 'technologies' => ['Node', 'AWS'], 'avatars' => [2, 6], 'progress' => 60, 'accent' => 'purple'],
            ],
        ],
        [
            'title' => 'Em Revisão',
            'tone' => 'yellow',
            'projects' => [
                ['title' => 'Dashboard Analytics BI', 'company' => 'FinTech Plus', 'priority' => 'Alta', 'critical' => true, 'type' => 'Dashboards', 'typeTone' => 'teal', 'owner' => 'Carlos Mendes', 'start' => '10 Fev', 'end' => '20 Jun', 'value' => 'R$ 52.000', 'technologies' => ['Python', 'Tableau'], 'avatars' => [3, 7], 'progress' => 92, 'accent' => 'green'],
            ],
        ],
        [
            'title' => 'Entregue',
            'tone' => 'green',
            'projects' => [
                ['title' => 'Landing Page Conversão', 'company' => 'Varejo Digital', 'completed' => true, 'type' => 'Landing Pages', 'typeTone' => 'pink', 'owner' => 'Ana Lima', 'start' => 'Entregue: 1 Jun', 'value' => 'R$ 12.000', 'technologies' => ['HTML', 'Webflow'], 'avatars' => [6], 'progress' => 100, 'accent' => 'green'],
                ['title' => 'Sistema de Gestão RH', 'company' => 'AgriTech', 'completed' => true, 'type' => 'Sistemas Web', 'typeTone' => 'blue', 'owner' => 'Camila Nunes', 'start' => 'Entregue: 15 Mai', 'value' => 'R$ 34.000', 'technologies' => ['React', 'Django'], 'avatars' => [1, 7], 'progress' => 100, 'accent' => 'green'],
            ],
        ],
    ];
@endphp

<x-admin.layout title="Projetos" page="Projetos">
    <div class="projects-page">
        <header class="projects-heading">
            <h1>Projetos</h1>
            <p>Gerencie todos os projetos ativos e históricos</p>
        </header>

        <section class="row g-3 g-xl-4 projects-metrics" aria-label="Indicadores de projetos">
            <div class="col-12 col-sm-6 col-xl-3"><x-admin.projects.metric-card label="Total de Projetos" value="7" note="ativos no momento" icon="bi-briefcase-fill" /></div>
            <div class="col-12 col-sm-6 col-xl-3"><x-admin.projects.metric-card label="Em Andamento" value="3" note="projetos em execução" icon="bi-hourglass-split" tone="orange" /></div>
            <div class="col-12 col-sm-6 col-xl-3"><x-admin.projects.metric-card label="Entregues" value="12" note="projetos histórico" icon="bi-check-circle-fill" tone="green" /></div>
            <div class="col-12 col-sm-6 col-xl-3"><x-admin.projects.metric-card label="Prazo Crítico" value="2" note="entrega em &lt;15 dias" icon="bi-exclamation-triangle-fill" tone="red" /></div>
        </section>

        <x-admin.projects.toolbar />

        <div class="project-board-scroll" tabindex="0" aria-label="Quadro Kanban de projetos">
            <div class="project-board">
                @foreach ($columns as $column)
                    <x-admin.projects.kanban-column :title="$column['title']" :count="count($column['projects'])" :tone="$column['tone']">
                        @foreach ($column['projects'] as $project)
                            <x-admin.projects.project-card
                                :title="$project['title']"
                                :company="$project['company']"
                                :priority="$project['priority'] ?? null"
                                :critical="$project['critical'] ?? false"
                                :completed="$project['completed'] ?? false"
                                :type="$project['type']"
                                :type-tone="$project['typeTone']"
                                :owner="$project['owner']"
                                :start="$project['start']"
                                :end="$project['end'] ?? null"
                                :value="$project['value']"
                                :technologies="$project['technologies']"
                                :avatars="$project['avatars']"
                                :progress="$project['progress']"
                                :accent="$project['accent']"
                            />
                        @endforeach
                    </x-admin.projects.kanban-column>
                @endforeach
            </div>
        </div>

        <footer class="project-timeline d-flex flex-wrap align-items-center gap-3">
            <button type="button" class="btn project-timeline-button">
                <i class="bi bi-diagram-3-fill" aria-hidden="true"></i>
                <span>Linha do Tempo</span>
                <span class="badge">Visualização Gantt</span>
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
            <p class="mb-0">Visualize o cronograma completo de todos os projetos em um gráfico Gantt interativo</p>
        </footer>
    </div>
</x-admin.layout>
