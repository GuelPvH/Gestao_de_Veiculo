<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Preload do OPcache — usado APENAS no target `prod` da imagem
|--------------------------------------------------------------------------
|
| Deliberadamente conservador: carrega o autoloader do Composer e nada mais.
|
| Pré-compilar as classes do framework em massa parece atraente, mas gera
| "Can't preload unlinked class" para toda classe cujas dependências ainda não
| foram resolvidas — ruído em cada boot do FPM, e em alguns casos classe
| ausente em runtime. Estenda daqui de forma medida, com `opcache_compile_file`
| sobre um conjunto que você tenha verificado.
|
| Em dev este arquivo não é carregado (ver docker/php/conf.d/opcache-dev.ini).
|
*/

$autoload = __DIR__.'/../vendor/autoload.php';

if (! is_file($autoload)) {
    // Imagem construída sem dependências: preload silencioso é melhor que
    // um fatal error impedindo o FPM de subir.
    return;
}

require_once $autoload;
