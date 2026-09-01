<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property LeadStatus $status
 * @property Carbon|null $desired_deadline
 * @property Carbon|null $converted_at
 */
#[Fillable([
    'name', 'company', 'email', 'phone', 'source', 'project_type', 'status',
    'estimated_value', 'desired_deadline', 'objective', 'notes', 'lost_reason',
])]
class Lead extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = ['status' => 'new'];

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<Proposal, $this> */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /** @return HasMany<Project, $this> */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'estimated_value' => 'decimal:2',
            'desired_deadline' => 'date',
            'converted_at' => 'datetime',
        ];
    }
}
