<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\BootstrapSuperAdmin;
use Illuminate\Console\Command;
use RuntimeException;

final class BootstrapSuperAdminCommand extends Command
{
    protected $signature = 'deploy:bootstrap-admin
                            {email : E-mail do primeiro Super Admin}
                            {--name=Administrador Deploy : Nome do primeiro Super Admin}';

    protected $description = 'Cria com segurança o primeiro Super Admin e envia um convite de definição de senha';

    public function handle(BootstrapSuperAdmin $bootstrap): int
    {
        $email = trim((string) $this->argument('email'));
        $name = trim((string) $this->option('name'));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Informe um endereço de e-mail válido.');

            return self::INVALID;
        }

        if ($name === '' || mb_strlen($name) > 255) {
            $this->error('O nome deve possuir entre 1 e 255 caracteres.');

            return self::INVALID;
        }

        try {
            $result = $bootstrap->handle($name, $email);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $action = $result['created'] ? 'criado' : 'promovido';
        $this->info("Super Admin {$action}. O convite para definição da senha foi enviado.");

        return self::SUCCESS;
    }
}
