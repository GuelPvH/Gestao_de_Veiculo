<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property ProjectStatus $status
 * @property Priority $priority
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $deadline
 */
#[Fillable([
    'client_id', 'lead_id', 'name', 'description', 'project_type', 'status',
    'priority', 'responsible_id', 'start_date', 'deadline', 'budget', 'progress',
    'technologies',
])]
class Project extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'status' => 'planning',
        'priority' => 'medium',
        'progress' => 0,
    ];

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** @return BelongsTo<Lead, $this> */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /** @return BelongsTo<User, $this> */
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsToMany<User, $this> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot(['project_role', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    /** @return HasMany<Task, $this> */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /** @return HasMany<Attachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /** @return HasMany<FinancialTransaction, $this> */
    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    /** @param Builder<Project> $query */
    public function scopeVisibleTo(Builder $query, User $user): void
    {
        if ($user->hasPermission('project.view_all')) {
            return;
        }

        $query->whereHas('members', function (Builder $query) use ($user): void {
            $query->where('users.id', $user->id)
                ->where(function (Builder $query): void {
                    $query->whereNull('project_members.left_at')
                        ->orWhere('project_members.left_at', '>', now());
                });
        });
    }

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'priority' => Priority::class,
            'start_date' => 'date',
            'deadline' => 'date',
            'budget' => 'decimal:2',
            'progress' => 'integer',
            'technologies' => 'array',
        ];
    }
}
