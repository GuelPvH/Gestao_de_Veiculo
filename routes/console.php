<?php

declare(strict_types=1);

use App\Console\Commands\FleetSummaryCommand;
use Illuminate\Support\Facades\Schedule;

// Mantém o serviço `scheduler` com trabalho real para executar: a cada cinco
// minutos o resumo da frota é reenfileirado, o que também exercita o worker.
Schedule::command(FleetSummaryCommand::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->description('Enfileira a geracao do resumo da frota');

// ---------------------------------------------------------------------------
// Backup (spatie/laravel-backup)
// Só funciona quando as credenciais S3 estiverem configuradas no .env.
// backup:monitor avisa se o backup parou de rodar silenciosamente.
// ---------------------------------------------------------------------------
Schedule::command('backup:run')->dailyAt('03:00');
Schedule::command('backup:clean')->dailyAt('04:00');
Schedule::command('backup:monitor')->dailyAt('05:00');
