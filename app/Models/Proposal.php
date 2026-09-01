<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property ProposalStatus $status
 * @property \Illuminate\Support\Carbon|null $valid_until
 * @property \Illuminate\Support\Carbon|null $accepted_at
 */
#[Fillable(['lead_id', 'client_id', 'title', 'description', 'value', 'status', 'valid_until'])]
class Proposal extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = ['status' => 'draft'];

    /** @return BelongsTo<Lead, $this> */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
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

    protected function casts(): array
    {
        return [
            'status' => ProposalStatus::class,
            'value' => 'decimal:2',
            'valid_until' => 'date',
            'accepted_at' => 'datetime',
        ];
    }
}
