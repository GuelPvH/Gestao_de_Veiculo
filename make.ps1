<#
.SYNOPSIS
    Runner de tarefas equivalente ao Makefile, para hosts Windows.

.DESCRIPTION
    O Windows nao traz `make`, e instalar um binario so para rodar o projeto
    contraria a premissa de "so Docker, Git e um editor". Este script expoe
    exatamente os mesmos alvos do Makefile e, como ele, encapsula Docker e
    nada mais: nenhuma linha aqui invoca php, composer, node, npm ou mysql
    do host.

    O arquivo e mantido em ASCII puro de proposito: o Windows PowerShell 5.1
    le .ps1 sem BOM usando a codepage ANSI, e um caractere acentuado em UTF-8
    vira aspa curva -- que o parser trata como delimitador de string.

.EXAMPLE
    .\make.ps1 setup
    .\make.ps1 artisan "migrate:status"
    .\make.ps1 composer "require vendor/pacote"
    .\make.ps1 destroy -Confirm yes
#>
param(
    [Parameter(Position = 0)]
    [string] $Target = 'help',

    [Parameter(Position = 1, ValueFromRemainingArguments = $true)]
    [string[]] $Rest,

    [string] $Confirm = ''
)

$ErrorActionPreference = 'Stop'
Set-Location -Path $PSScriptRoot

# -----------------------------------------------------------------------------
# Helpers
#
# Todos recebem UM array explicito. Usar `$args` com
# `ValueFromRemainingArguments` aninha o array e o cast [string[]] colapsa tudo
# em uma unica string -- o docker recebe "compose build" como um argumento so e
# responde "unknown command".
# -----------------------------------------------------------------------------
function Invoke-Docker([string[]] $DockerArgs) {
    Write-Host "> docker $($DockerArgs -join ' ')" -ForegroundColor DarkGray

    # O Windows PowerShell 5.1 transforma cada linha de stderr de um executavel
    # nativo em ErrorRecord quando a saida esta redirecionada. O docker escreve
    # o progresso em stderr, entao com ErrorActionPreference=Stop o script
    # abortaria na primeira linha de build -- sem nenhuma falha real.
    # O que decide sucesso ou fracasso aqui e o exit code, nada mais.
    $previous = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'
    try {
        & docker @DockerArgs
    }
    finally {
        $ErrorActionPreference = $previous
    }

    if ($LASTEXITCODE -ne 0) {
        throw "Falhou (exit $LASTEXITCODE): docker $($DockerArgs -join ' ')"
    }
}

function Compose([string[]] $ComposeArgs) {
    Invoke-Docker (@('compose') + $ComposeArgs)
}

# `run --rm` passa pelo entrypoint, que ja derruba o privilegio para www-data.
function RunApp ([string[]] $CmdArgs) { Compose (@('run', '--rm', 'app') + $CmdArgs) }
function RunNode([string[]] $CmdArgs) { Compose (@('run', '--rm', 'vite') + $CmdArgs) }

# `exec` NAO passa pelo entrypoint: sem o -u explicito os arquivos nasceriam
# pertencendo ao root.
function ExecApp([string[]] $CmdArgs) { Compose (@('exec', '-u', 'www-data', 'app') + $CmdArgs) }

function Get-AppPort {
    $line = Select-String -Path '.env' -Pattern '^APP_PORT=(\d+)' -ErrorAction SilentlyContinue
    if ($line) { return $line.Matches[0].Groups[1].Value }
    return '8000'
}

function Assert-Confirmed([string] $What, [string] $Hint) {
    if ($Confirm -ne 'yes') {
        Write-Host "RECUSADO: $What" -ForegroundColor Red
        if ($Hint) { Write-Host $Hint -ForegroundColor Yellow }
        Write-Host "Se e isso mesmo que voce quer: .\make.ps1 $Target -Confirm yes" -ForegroundColor Yellow
        exit 1
    }
}

# Argumentos livres do usuario, normalizados em tokens.
$extra = @()
if ($Rest) {
    $extra = @(($Rest -join ' ') -split '\s+' | Where-Object { $_ -ne '' })
}

# -----------------------------------------------------------------------------
# Alvos
# -----------------------------------------------------------------------------
switch ($Target) {

    'help' {
        @'
Alvos disponiveis (mesmos do Makefile):

  Ciclo de vida
  - setup            Instalacao completa do zero (idempotente)
  - up               Sobe os containers
  - down             Para os containers (volumes e dados PRESERVADOS)
  - restart          Reinicia os containers
  - build            Constroi as imagens
  - rebuild          Reconstroi ignorando cache
  - ps               Estado dos containers
  - logs             Segue o log de todos os servicos
  - logs-app         Segue o log de app, nginx e queue
  - nginx-test       Valida a configuracao do nginx

  Shell e execucao
  - shell            Shell no container app como www-data
  - shell-root       Shell no container app como root
  - db-shell         Cliente MySQL dentro do container do banco
  - tinker           REPL do Laravel
  - artisan  "<cmd>" Comando artisan
  - composer "<cmd>" Comando composer
  - npm      "<cmd>" Comando npm
  - install          Instala dependencias PHP e JS
  - migrate          Aplica as migrations
  - seed             Roda os seeders
  - key              Gera a APP_KEY
  - queue-restart    Faz o worker (Horizon) recarregar o codigo

  Qualidade (ordem de check: lint -> rector -> analyse -> test)
  - test             Suite de testes
  - test-coverage    Testes com cobertura minima de 80%
  - lint             Pint em modo verificacao (modo do CI)
  - lint-fix         Pint corrigindo so o que mudou no git
  - analyse          Analise estatica (Larastan/PHPStan)
  - rector           Refactors sugeridos, sem aplicar
  - rector-apply     Aplica os refactors
  - insights         PHP Insights (opcional)
  - check            Pipeline completo de qualidade
  - audit            Auditoria Container First
  - hook-install     Ativa os git hooks (CaptainHook)

  Destrutivos (exigem -Confirm yes)
  - fresh            APAGA E RECRIA AS TABELAS
  - destroy          APAGA OS VOLUMES (banco inclusive)
  - db-dump          Dump do banco para dump-AAAAMMDD-HHMM.sql
'@ | Write-Host
    }

    'setup' {
        if (-not (Test-Path '.env')) { Copy-Item '.env.example' '.env' }
        Compose @('build')
        RunApp  @('composer', 'install', '--no-interaction', '--prefer-dist')
        RunNode @('npm', 'ci')
        Compose @('up', '-d')
        ExecApp @('sh', '-c', 'grep -q "^APP_KEY=base64:" .env || php artisan key:generate --force')
        ExecApp @('php', 'artisan', 'migrate', '--force')
        ExecApp @('php', 'artisan', 'storage:link', '--force')
        RunNode @('npm', 'run', 'build')
        Write-Host ''
        Write-Host "Pronto. Aplicacao em http://localhost:$(Get-AppPort)" -ForegroundColor Green
    }

    'up'         { Compose @('up', '-d') }
    'down'       { Compose @('down') }
    'restart'    { Compose @('restart') }
    'build'      { Compose @('build') }
    'rebuild'    { Compose @('build', '--no-cache'); Compose @('up', '-d', '--force-recreate') }
    'ps'         { Compose @('ps') }
    'logs'       { Compose @('logs', '-f', '--tail=100') }
    'logs-app'   { Compose @('logs', '-f', '--tail=100', 'app', 'nginx', 'queue') }
    'nginx-test' { Compose @('exec', 'nginx', 'nginx', '-t') }

    'shell'      { ExecApp @('sh') }
    'shell-root' { Compose @('exec', 'app', 'sh') }
    'db-shell'   { Compose @('exec', 'mysql', 'sh', '-c', 'mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"') }
    'tinker'     { ExecApp @('php', 'artisan', 'tinker') }

    'artisan'    { ExecApp (@('php', 'artisan') + $extra) }
    'composer'   { RunApp  (@('composer') + $extra) }
    'npm'        { RunNode (@('npm') + $extra) }

    'install' {
        RunApp  @('composer', 'install', '--no-interaction', '--prefer-dist')
        RunNode @('npm', 'ci')
    }

    'migrate'       { ExecApp @('php', 'artisan', 'migrate') }
    'seed'          { ExecApp @('php', 'artisan', 'db:seed') }
    'key'           { ExecApp @('php', 'artisan', 'key:generate') }
    'queue-restart' { ExecApp @('php', 'artisan', 'horizon:terminate') }

    'test'          { ExecApp @('./vendor/bin/pest') }
    'test-coverage' { ExecApp @('./vendor/bin/pest', '--coverage', '--min=80') }
    'lint'          { ExecApp @('./vendor/bin/pint', '--test') }
    'lint-fix'      { ExecApp @('./vendor/bin/pint', '--dirty') }
    'analyse'       { ExecApp @('./vendor/bin/phpstan', 'analyse', '--memory-limit=1G') }
    'rector'        { ExecApp @('./vendor/bin/rector', 'process', '--dry-run') }
    'rector-apply'  { ExecApp @('./vendor/bin/rector', 'process') }
    'insights'      { ExecApp @('./vendor/bin/phpinsights', '--no-interaction') }

    'audit' {
        # Roda dentro do container: nem `sh` e garantido no host Windows.
        RunApp @('sh', 'scripts/audit-container-first.sh')
    }

    'check' {
        ExecApp @('./vendor/bin/pint', '--test')
        ExecApp @('./vendor/bin/rector', 'process', '--dry-run')
        ExecApp @('./vendor/bin/phpstan', 'analyse', '--memory-limit=1G')
        ExecApp @('./vendor/bin/pest')
    }

    'hook-install' {
        RunApp @('vendor/bin/captainhook', 'install', '--force')
    }

    'fresh' {
        Assert-Confirmed "'fresh' descarta TODOS os dados do banco."
        ExecApp @('php', 'artisan', 'migrate:fresh', '--seed')
    }

    'destroy' {
        Assert-Confirmed "'destroy' remove os volumes - o banco some junto." "Faca um dump antes:  .\make.ps1 db-dump"
        Compose @('down', '-v')
    }

    'db-dump' {
        $file = "dump-$(Get-Date -Format 'yyyyMMdd-HHmm').sql"
        docker compose exec -T mysql sh -c 'mysqldump -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' |
            Out-File -FilePath $file -Encoding utf8
        if ($LASTEXITCODE -ne 0) { throw "mysqldump falhou (exit $LASTEXITCODE)" }
        Write-Host "Dump gravado em $file" -ForegroundColor Green
    }

    default {
        Write-Host "Alvo desconhecido: $Target" -ForegroundColor Red
        Write-Host 'Use: .\make.ps1 help'
        exit 1
    }
}
