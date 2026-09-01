<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property TaskStatus $status
 * @property Priority $priority
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property Project $project
 */
#[Fillable([
    'project_id', 'title', 'description', 'status', 'priority', 'assigned_to',
    'estimated_minutes', 'due_date', 'started_at', 'completed_at',
])]
class Task extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'status' => 'backlog',
        'priority' => 'medium',
    ];

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<TaskComment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    /** @return HasMany<Attachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /** @param Builder<Task> $query */
    public function scopeVisibleTo(Builder $query, User $user): void
    {
        if ($user->hasPermission('task.view_all')) {
            return;
        }

        $query->whereIn('project_id', Project::query()->visibleTo($user)->select('id'));
    }

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => Priority::class,
            'estimated_minutes' => 'integer',
            'due_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
