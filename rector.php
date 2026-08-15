<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelSetList;

/*
|--------------------------------------------------------------------------
| Rector
|--------------------------------------------------------------------------
|
| Rector em `--dry-run` no CI é excelente; Rector aplicando automaticamente em
| CI é receita de desastre. Rode `make rector`, LEIA o diff, e só então
| `make rector-apply`.
|
| Nada de `LevelSetList::UP_TO_PHP_84`: a API de níveis por constante foi
| substituída no Rector 2.x pelos métodos `withPhpSets()` / `withPhpVersion()`,
| que derivam a versão do próprio composer.json.
|
*/

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap/app.php',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        __DIR__.'/bootstrap/cache',
        __DIR__.'/storage',
        __DIR__.'/vendor',

        // `Job::dispatch()` é a forma idiomática e mais legível no Laravel.
        // Trocar por `dispatch(new Job())` não corrige nada e ainda esconde
        // o nome do job atrás de um helper global.
        DispatchToHelperFunctionsRector::class,
    ])
    // PHP 8.4 — a versão vem do `require.php` do composer.json.
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
        privatization: true,
    )
    ->withSets([
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_IF_HELPERS,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
    ])
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    // Cache dentro do container, fora do bind mount — mesmo motivo do
    // `tmpDir` em phpstan.neon.
    ->withCache('/tmp/rector');
