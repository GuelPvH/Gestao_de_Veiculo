<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property ServiceStatus $status
 * @property Carbon|null $published_at
 */
#[Fillable([
    'name', 'slug', 'short_description', 'description', 'features', 'tags',
    'base_price', 'status', 'sort_order', 'published_at',
])]
class Service extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'status' => 'draft',
        'sort_order' => 0,
    ];

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'tags' => 'array',
            'base_price' => 'decimal:2',
            'status' => ServiceStatus::class,
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }
}
