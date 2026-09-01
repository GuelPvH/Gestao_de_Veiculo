<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\FinancialTransactionStatus;
use App\Enums\FinancialTransactionType;
use App\Enums\LeadStatus;
use App\Enums\ProjectStatus;
use App\Models\FinancialTransaction;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

final readonly class BuildDashboard
{
    /** @return array<string, mixed> */
    public function handle(User $user): array
    {
        if ($user->hasPermission('dashboard.view_personal')) {
            return $this->personal($user);
        }

        if ($user->hasPermission('dashboard.view_financial')) {
            return $this->financial();
        }

        return $this->general($user);
    }

    /** @return array<string, mixed> */
    private function personal(User $user): array
    {
        $taskCounts = Task::query()
            ->visibleTo($user)
            ->where('assigned_to', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'scope' => 'personal',
            'projects' => Project::query()->visibleTo($user)->count(),
            'tasks' => $taskCounts,
            'upcoming_deadlines' => Task::query()
                ->visibleTo($user)
                ->where('assigned_to', $user->id)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '>=', today())
                ->orderBy('due_date')
                ->limit(10)
                ->get(['id', 'project_id', 'title', 'status', 'due_date']),
        ];
    }

    /** @return array<string, mixed> */
    private function financial(): array
    {
        return [
            'scope' => 'financial',
            'income' => FinancialTransaction::query()
                ->where('type', FinancialTransactionType::Income)
                ->where('status', FinancialTransactionStatus::Paid)
                ->sum('amount'),
            'expenses' => FinancialTransaction::query()
                ->where('type', FinancialTransactionType::Expense)
                ->where('status', FinancialTransactionStatus::Paid)
                ->sum('amount'),
            'overdue' => FinancialTransaction::query()
                ->where('status', FinancialTransactionStatus::Overdue)
                ->sum('amount'),
            'pending_count' => FinancialTransaction::query()
                ->where('status', FinancialTransactionStatus::Pending)
                ->count(),
        ];
    }

    /** @return array<string, mixed> */
    private function general(User $user): array
    {
        $totalLeads = Lead::query()->count();
        $wonLeads = Lead::query()->where('status', LeadStatus::Won)->count();
        $dashboard = [
            'scope' => 'general',
            'leads' => $totalLeads,
            'active_projects' => Project::query()->whereIn('status', [
                ProjectStatus::InAnalysis,
                ProjectStatus::InProgress,
                ProjectStatus::InReview,
            ])->count(),
            'conversion_rate' => $totalLeads === 0
                ? 0.0
                : round(($wonLeads / $totalLeads) * 100, 2),
            'recent_leads' => Lead::query()
                ->latest('id')
                ->limit(10)
                ->get(['id', 'name', 'company', 'status', 'created_at']),
        ];

        if ($user->hasPermission('finance.view_any')) {
            $dashboard['financial'] = $this->financial();
        }

        return $dashboard;
    }
}
