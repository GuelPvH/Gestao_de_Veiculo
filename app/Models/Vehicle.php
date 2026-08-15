<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VehicleStatus;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $plate
 * @property string $brand
 * @property string $model
 * @property int $year
 * @property VehicleStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'plate',
        'brand',
        'model',
        'year',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'status' => VehicleStatus::class,
        ];
    }
}
