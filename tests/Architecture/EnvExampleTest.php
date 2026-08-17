<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

/*
|--------------------------------------------------------------------------
| O .env.example é um contrato
|--------------------------------------------------------------------------
|
| Ele é a primeira coisa que alguém copia ao clonar o projeto, e a única fonte
| que diz quais variáveis existem. Duas formas de ele apodrecer, silenciosas:
|
|   1. CHAVE MORTA — a configuração deixou de ler a variável (renomeada,
|      removida, ou o pacote mudou o nome), mas a linha continua ali. Quem
|      clona preenche, nada acontece, e a pessoa perde uma tarde procurando o
|      erro em outro lugar. Foi exatamente o caso do `MAIL_ENCRYPTION`, que o
|      Laravel 11 substituiu por `MAIL_SCHEME`.
|
|   2. CHAVE DUPLICADA — a mesma variável em dois lugares do arquivo. A última
|      ganha, e quem edita a primeira jura que configurou.
|
| O caminho inverso (variável lida pela configuração e ausente daqui) NÃO é
| verificado: as configs publicadas por pacote leem dezenas de chaves opcionais
| — MEMCACHED_PASSWORD, PAPERTRAIL_PORT, SLACK_BOT_USER_OAUTH_TOKEN — que não
| têm por que estar no exemplo. Um teste que as cobrasse só ensinaria o time a
| ignorá-lo. Essa parte fica com o checklist do PR.
|
*/

/**
 * Chaves que a documentação precisa expor mas que NÃO aparecem em `config/`,
 * `compose.yaml` ou `docker/`. Cada uma é lida por algo fora do repositório.
 *
 * Esta lista é curta de propósito. Se ela começar a crescer, o problema
 * provavelmente é chave morta sendo mantida — não exceção legítima.
 *
 * @var list<string>
 */
$lidasForaDoRepositorio = [
    // Lida pelo próprio binário do Docker Compose (seleciona os profiles),
    // nunca referenciada dentro do compose.yaml.
    'COMPOSE_PROFILES',

    // O projeto não publica `config/broadcasting.php`; quem lê esta chave é a
    // configuração padrão do framework, dentro do vendor/.
    'BROADCAST_CONNECTION',
];

/**
 * Todo o texto onde uma variável de ambiente pode ser legitimamente lida:
 * configuração do Laravel, orquestração e imagens.
 */
function textoQueLeVariaveisDeAmbiente(): string
{
    $raiz = dirname(__DIR__, 2);

    $arquivos = Finder::create()
        ->files()
        ->in([$raiz.'/config', $raiz.'/docker'])
        ->name(['*.php', '*.yaml', '*.yml', '*.sh', '*.conf', '*.ini', 'Dockerfile', 'entrypoint*']);

    $texto = (string) file_get_contents($raiz.'/compose.yaml');

    foreach ($arquivos as $arquivo) {
        $texto .= (string) file_get_contents($arquivo->getRealPath());
    }

    return $texto;
}

/**
 * @return list<string>
 */
function chavesDoEnvExample(): array
{
    $conteudo = (string) file_get_contents(dirname(__DIR__, 2).'/.env.example');

    preg_match_all('/^([A-Z][A-Z0-9_]*)=/m', $conteudo, $encontradas);

    // Sem `@var` aqui: o PHPStan já deduz `list<non-falsy-string>` do
    // preg_match_all, e anotar `list<string>` seria alargar o tipo que ele
    // conhece — o nível 8 reclama disso, com razão.
    return $encontradas[1];
}

it('nao tem chave morta no .env.example', function () use ($lidasForaDoRepositorio): void {
    $texto = textoQueLeVariaveisDeAmbiente();

    $mortas = array_values(array_filter(
        chavesDoEnvExample(),
        fn (string $chave): bool => ! in_array($chave, $lidasForaDoRepositorio, strict: true)
            && preg_match('/\b'.preg_quote($chave, '/').'\b/', $texto) !== 1,
    ));

    expect($mortas)->toBe([], sprintf(
        "Chave(s) no .env.example que ninguem le: %s.\n".
        'Ou a configuracao que a lia foi removida/renomeada (apague a linha do .env.example), '.
        'ou ela e lida fora do repositorio (adicione a $lidasForaDoRepositorio, com o motivo).',
        implode(', ', $mortas),
    ));
});

it('nao tem chave duplicada no .env.example', function (): void {
    $chaves = chavesDoEnvExample();

    $duplicadas = array_values(array_unique(array_diff_assoc($chaves, array_unique($chaves))));

    expect($duplicadas)->toBe([], sprintf(
        'Chave(s) declarada(s) mais de uma vez no .env.example: %s. A ultima vence, '.
        'e quem editar a primeira vai jurar que configurou.',
        implode(', ', $duplicadas),
    ));
});
