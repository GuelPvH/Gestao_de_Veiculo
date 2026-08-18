<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /*
    |--------------------------------------------------------------------------
    | Vite desligado na suíte inteira
    |--------------------------------------------------------------------------
    |
    | O `@vite()` do layout resolve os assets lendo `public/build/manifest.json`.
    | Esse arquivo é produto do `npm run build` e não existe numa checagem de
    | PHP: sem ele o Blade lança `ViteManifestNotFoundException` e QUALQUER
    | teste que renderize uma view vira 500.
    |
    | Fazer a suíte depender do build seria pior do que parece. O resultado
    | passaria a mudar conforme a máquina: verde para quem rodou `npm run build`
    | antes, vermelho para quem não rodou e vermelho no CI, onde o build do
    | front-end roda DEPOIS dos testes. Teste de PHP não deve ter opinião sobre
    | o estado do toolchain de JS.
    |
    | O manifest continua coberto onde ele de fato importa, com os assets reais:
    | o job `smoke` sobe a stack completa depois do build e bate em `/`, e o job
    | `producao` verifica o arquivo dentro da imagem. Se o `@vite()` quebrar, é
    | lá que o CI acusa — com sinal honesto, não com um 500 fora de contexto.
    |
    */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
