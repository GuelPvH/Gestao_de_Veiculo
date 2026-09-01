<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Client;
use App\Models\FinancialTransaction;
use App\Models\Lead;
use App\Models\Observers\AuditObserver;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Proposal;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(fn (User $user): ?bool => $user->isSuperAdmin() ? true : null);
        Gate::define('viewPulse', fn (User $user): bool => $user->hasPermission('security.view'));

        Client::observe(AuditObserver::class);
        FinancialTransaction::observe(AuditObserver::class);
        Lead::observe(AuditObserver::class);
        Project::observe(AuditObserver::class);
        ProjectMember::observe(AuditObserver::class);
        Proposal::observe(AuditObserver::class);
        Role::observe(AuditObserver::class);
        Service::observe(AuditObserver::class);
        Task::observe(AuditObserver::class);
        TaskComment::observe(AuditObserver::class);
        User::observe(AuditObserver::class);

        RateLimiter::for('api', fn (Request $request) => $request->user()
            ? Limit::perMinute(120)->by($request->user()->id)
            : Limit::perMinute(20)->by($request->ip()));

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->ip().'|'.$request->input('email')));
        RateLimiter::for('public-leads', fn (Request $request) => Limit::perHour(5)->by($request->ip()));
    }
}
