<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ImmutableLog;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property bool $success */
#[Fillable([
    'user_id', 'email_hash', 'event', 'success', 'failure_reason',
    'ip_address', 'user_agent', 'metadata',
])]
class AuthenticationLog extends Model
{
    use ImmutableLog;

    public const UPDATED_AT = null;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'metadata' => 'array',
        ];
    }
}
