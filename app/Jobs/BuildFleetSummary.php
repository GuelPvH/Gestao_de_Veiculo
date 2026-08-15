<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\SummariseFleet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Job de verificação do ambiente: prova que a fila está processando de ponta a
 * ponta (Redis -> worker -> cache -> log). Também é o job agendado pelo
 * scheduler, então cobre os dois serviços de longa duração.
 */
final class BuildFleetSummary implements ShouldQueue
{
    use Queueable;

    public const string CACHE_KEY = 'frota.resumo';

    public int $tries = 3;

    public function handle(SummariseFleet $summariseFleet): void
    {
        $summary = $summariseFleet->handle();

        Cache::put(self::CACHE_KEY, [
            ...$summary,
            'gerado_em' => now()->toIso8601String(),
        ], now()->addDay());

        Log::info('Resumo da frota gerado.', $summary);
    }
}
