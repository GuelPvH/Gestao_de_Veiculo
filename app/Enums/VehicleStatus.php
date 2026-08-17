<?php

declare(strict_types=1);

namespace App\Enums;

enum VehicleStatus: string
{
    case Available = 'disponivel';
    case InUse = 'em_uso';
    case Maintenance = 'manutencao';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponível',
            self::InUse => 'Em uso',
            self::Maintenance => 'Em manutenção',
        };
    }

    /**
     * Classe do badge Bootstrap correspondente ao status.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'text-bg-success',
            self::InUse => 'text-bg-primary',
            self::Maintenance => 'text-bg-warning',
        };
    }
}
