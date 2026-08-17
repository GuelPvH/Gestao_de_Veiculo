<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
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

        // Assinatura de método de Policy é CONTRATO do framework, não código
        // morto: o Laravel chama `create(User $user)` e
        // `update(User $user, Vehicle $vehicle)`. Hoje as regras devolvem `true`
        // sem consultar os argumentos (não há papéis no projeto ainda), e o
        // Rector conclui que os parâmetros são inúteis. Aplicar isso deixaria a
        // Policy sem o `$user` — exatamente o argumento que a primeira regra de
        // permissão real vai usar.
        RemoveUnusedPublicMethodParameterRector::class => [
            __DIR__.'/app/Policies',
        ],

        // `config/sentry.php` precisa do callable como array `[classe, método]`
        // — `config:cache` serializa a config com `var_export`, que não sabe
        // representar uma Closure, e um callable de primeira classe
        // (`Classe::metodo(...)`) É uma Closure em tempo de execução.
        ArrayToFirstClassCallableRector::class => [
            __DIR__.'/config/sentry.php',
        ],
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
