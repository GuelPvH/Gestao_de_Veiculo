<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SecuritySeverity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'event_type', 'severity', 'ip_address', 'user_agent', 'metadata'])]
class SecurityEvent extends Model
{
    public const UPDATED_AT = null;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'severity' => SecuritySeverity::class,
            'metadata' => 'array',
        ];
    }
}
