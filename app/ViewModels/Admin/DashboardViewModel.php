<?php

declare(strict_types=1);

namespace App\ViewModels\Admin;

final readonly class DashboardViewModel
{
    /**
     * @return array{
     *     metrics: list<array<string, string>>,
     *     months: list<array{name: string, height: int}>,
     *     statuses: list<array{label: string, value: int, tone: string}>,
     *     activities: list<array<string, string>>,
     *     leads: list<array<string, string>>
     * }
     */
    public function data(): array
    {
        return [
            'metrics' => [
                ['label' => 'Leads este mês', 'value' => '28', 'icon' => 'bi-person-plus-fill', 'tone' => 'blue', 'note' => '+12% este mês'],
                ['label' => 'Projetos Ativos', 'value' => '7', 'icon' => 'bi-file-earmark-text-fill', 'tone' => 'purple', 'note' => 'em andamento'],
                ['label' => 'Receita Total', 'value' => 'R$ 142.500', 'icon' => 'bi-check-circle-fill', 'tone' => 'green', 'note' => 'este mês'],
                ['label' => 'Taxa de Conversão', 'value' => '32%', 'icon' => 'bi-hourglass-split', 'tone' => 'yellow', 'note' => 'leads → projetos'],
            ],
            'months' => [
                ['name' => 'Jan', 'height' => 43],
                ['name' => 'Fev', 'height' => 58],
                ['name' => 'Mar', 'height' => 67],
                ['name' => 'Abr', 'height' => 53],
                ['name' => 'Mai', 'height' => 77],
                ['name' => 'Jun', 'height' => 86],
            ],
            'statuses' => [
                ['label' => 'Em Andamento', 'value' => 4, 'tone' => 'primary'],
                ['label' => 'Concluído', 'value' => 2, 'tone' => 'success'],
                ['label' => 'Pausado', 'value' => 1, 'tone' => 'warning'],
            ],
            'activities' => [
                ['icon' => 'bi-person-plus-fill', 'tone' => 'blue', 'title' => 'Lead recebido — João Silva', 'detail' => 'Formulário do site — 15 Jun 2025, 09:42'],
                ['icon' => 'bi-file-earmark-text-fill', 'tone' => 'purple', 'title' => 'Proposta enviada — Carlos Mendes', 'detail' => 'Dashboard BI — 14 Jun 2025, 14:20'],
                ['icon' => 'bi-telephone-fill', 'tone' => 'green', 'title' => 'Projeto iniciado — Ana Lima', 'detail' => 'Landing Page Varejo — 13 Jun 2025, 10:05'],
                ['icon' => 'bi-eye-fill', 'tone' => 'yellow', 'title' => 'Lead visualizado — Maria Santos', 'detail' => 'Admin User — 13 Jun 2025, 11:00'],
                ['icon' => 'bi-calendar-check-fill', 'tone' => 'blue', 'title' => 'Reunião agendada — Rafael Moura', 'detail' => 'EduPlatz — 12 Jun 2025, 16:00'],
                ['icon' => 'bi-x-lg', 'tone' => 'red', 'title' => 'Lead perdido — Camila Nunes', 'detail' => 'AgriTech — 11 Jun 2025, 09:15'],
            ],
            'leads' => [
                ['id' => '01', 'name' => 'João Silva', 'company' => 'TechBR', 'avatar' => 'joao-silva.png', 'type' => 'Sistema Web', 'typeTone' => 'primary', 'status' => 'Novo', 'statusTone' => 'primary', 'date' => '15 Jun 2025'],
                ['id' => '02', 'name' => 'Maria Santos', 'company' => 'Logística SA', 'avatar' => 'maria-santos.png', 'type' => 'Soft. Custom', 'typeTone' => 'primary', 'status' => 'Em Análise', 'statusTone' => 'warning', 'date' => '14 Jun 2025'],
                ['id' => '03', 'name' => 'Carlos Mendes', 'company' => 'FinTech Plus', 'avatar' => 'carlos-mendes.png', 'type' => 'Dashboard BI', 'typeTone' => 'purple', 'status' => 'Prop. Enviada', 'statusTone' => 'purple', 'date' => '13 Jun 2025'],
                ['id' => '04', 'name' => 'Ana Lima', 'company' => 'Varejo Digital', 'avatar' => 'ana-lima.png', 'type' => 'Landing Page', 'typeTone' => 'danger', 'status' => 'Fechado', 'statusTone' => 'success', 'date' => '12 Jun 2025'],
            ],
        ];
    }
}
