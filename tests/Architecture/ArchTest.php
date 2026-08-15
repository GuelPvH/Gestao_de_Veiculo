<?php

declare(strict_types=1);

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Queue\ShouldQueue;

/*
|--------------------------------------------------------------------------
| Testes de arquitetura
|--------------------------------------------------------------------------
|
| Custam milissegundos e impedem que a estrutura apodreça. O teste de debug
| esquecido sozinho já paga o custo: pega um `dd()` antes do code review, não
| depois do deploy.
|
*/

arch()->preset()->php();
arch()->preset()->security();

arch('sem debug esquecido no codigo')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r', 'die', 'exit'])
    ->not->toBeUsed();

// Duas asserções separadas de propósito: `ignoring()` se aplica apenas à
// última expectation da cadeia, então encadear `toBeFinal()` com `toExtend()`
// faria a regra do "final" cair sobre o próprio Controller base — que é
// abstrato e, portanto, nunca pode ser final.
arch('controllers estendem o controller base')
    ->expect('App\Http\Controllers')
    ->toBeClasses()
    ->toExtend(Controller::class)
    ->ignoring(Controller::class);

arch('controllers concretos sao finais')
    ->expect('App\Http\Controllers')
    ->toBeFinal()
    ->ignoring(Controller::class);

arch('models so aparecem no dominio, nunca direto na view')
    ->expect('App\Models')
    ->toOnlyBeUsedIn([
        'App\Actions',
        'App\Http',
        'App\Models',
        // Gates de autorização (Pulse, Horizon) tipam o argumento contra o
        // model User — não é acesso direto na view, é wiring de bootstrap.
        'App\Providers',
        'App\Policies',
        'Database',
        'Tests',
    ]);

arch('actions tem um unico ponto de entrada')
    ->expect('App\Actions')
    ->toBeClasses()
    ->toBeFinal()
    ->toHaveMethod('handle');

arch('actions nao dependem de facade')
    ->expect('Illuminate\Support\Facades')
    ->not->toBeUsedIn('App\Actions');

arch('jobs sao enfileiraveis e finais')
    ->expect('App\Jobs')
    ->toBeFinal()
    ->toImplement(ShouldQueue::class);

arch('enums nao carregam estado')
    ->expect('App\Enums')
    ->toBeEnums();

arch('controllers de API usam strict types')
    ->expect('App\Http\Controllers\Api')
    ->toUseStrictTypes();
