<?php

declare(strict_types=1);

namespace App\ViewModels\Admin;

final readonly class ProjectBoardViewModel
{
    /** @return array{columns: list<array<string, mixed>>} */
    public function data(): array
    {
        return ['columns' => [
            [
                'title' => 'Em Análise',
                'tone' => 'slate',
                'projects' => [
                    ['title' => 'Prontuário Eletrônico', 'company' => 'Saúde Tech', 'priority' => 'high', 'priorityLabel' => 'Alta', 'type' => 'Sistema Web', 'typeTone' => 'blue', 'owner' => 'Beatriz Rocha', 'start' => 'Início: 10 Jun', 'end' => '90 dias', 'value' => 'R$ 28.000', 'technologies' => ['React', 'Node', 'MongoDB'], 'avatars' => [1], 'progress' => 0, 'accent' => 'slate'],
                    ['title' => 'Plataforma de Ensino EduPlat', 'company' => 'Rafael Moura', 'priority' => 'medium', 'priorityLabel' => 'Média', 'type' => 'Software Custom', 'typeTone' => 'purple', 'owner' => 'Rafael Moura', 'start' => 'Início: 9 Jun', 'end' => '120 dias', 'value' => 'R$ 45.000', 'technologies' => ['Next.js', 'PostgreSQL'], 'avatars' => [3], 'progress' => 0, 'accent' => 'slate'],
                ],
            ],
            [
                'title' => 'Em Andamento',
                'tone' => 'blue',
                'projects' => [
                    ['title' => 'Sistema ERP Industrial', 'company' => 'Ind. Moderna', 'priority' => 'high', 'priorityLabel' => 'Alta', 'critical' => true, 'type' => 'Sistemas Web', 'typeTone' => 'blue', 'owner' => 'Pedro Costa', 'start' => '1 Abr', 'end' => '15 Jul', 'value' => 'R$ 85.000', 'technologies' => ['React', 'Node'], 'avatars' => [4, 8], 'progress' => 78, 'accent' => 'blue'],
                    ['title' => 'Plataforma CRM Comercial', 'company' => 'Logística SA', 'priority' => 'high', 'priorityLabel' => 'Alta', 'type' => 'Software Custom', 'typeTone' => 'purple', 'owner' => 'Maria Santos', 'start' => '15 Mar', 'end' => 'Em andamento', 'value' => 'R$ 62.000', 'technologies' => ['Vue.js', 'Laravel'], 'avatars' => [5, 9], 'progress' => 45, 'accent' => 'indigo'],
                    ['title' => 'API Gateway Financeiro', 'company' => 'TechBR', 'priority' => 'medium', 'priorityLabel' => 'Média', 'type' => 'APIs', 'typeTone' => 'orange', 'owner' => 'João Silva', 'start' => '1 Mai', 'end' => 'Em andamento', 'value' => 'R$ 38.000', 'technologies' => ['Node', 'AWS'], 'avatars' => [2, 6], 'progress' => 60, 'accent' => 'purple'],
                ],
            ],
            [
                'title' => 'Em Revisão',
                'tone' => 'yellow',
                'projects' => [
                    ['title' => 'Dashboard Analytics BI', 'company' => 'FinTech Plus', 'priority' => 'high', 'priorityLabel' => 'Alta', 'critical' => true, 'type' => 'Dashboards', 'typeTone' => 'teal', 'owner' => 'Carlos Mendes', 'start' => '10 Fev', 'end' => '20 Jun', 'value' => 'R$ 52.000', 'technologies' => ['Python', 'Tableau'], 'avatars' => [3, 7], 'progress' => 92, 'accent' => 'green'],
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
        ]];
    }
}
