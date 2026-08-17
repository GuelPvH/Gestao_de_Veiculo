<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\BuildFleetSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

final class FleetSummaryCommand extends Command
{
    protected $signature = 'frota:resumo
                            {--show : Apenas exibe o ultimo resumo em cache, sem enfileirar}';

    protected $description = 'Enfileira a geracao do resumo da frota (ou exibe o ultimo resultado)';

    public function handle(): int
    {
        if ($this->option('show')) {
            $summary = Cache::get(BuildFleetSummary::CACHE_KEY);

            if ($summary === null) {
                $this->warn('Nenhum resumo em cache. Rode `php artisan frota:resumo` primeiro.');

                return self::FAILURE;
            }

            $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '');

            return self::SUCCESS;
        }

        BuildFleetSummary::dispatch();

        $this->info('Job BuildFleetSummary enfileirado.');

        return self::SUCCESS;
    }
}
