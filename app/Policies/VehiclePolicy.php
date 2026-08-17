<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

/**
 * Autorização de veículo.
 *
 * O Laravel descobre esta classe automaticamente pela convenção de nome
 * (`App\Models\Vehicle` → `App\Policies\VehiclePolicy`): não há registro a
 * fazer em nenhum provider.
 *
 * ------------------------------------------------------------------------
 * Por que os métodos devolvem `true` se qualquer pessoa autenticada pode?
 * ------------------------------------------------------------------------
 * Porque o projeto ainda não tem papéis (perfil, permissão, dono do veículo).
 * A regra de HOJE é "autenticado pode" — e ela precisa estar escrita em algum
 * lugar. Este arquivo é esse lugar.
 *
 * Quando o primeiro papel aparecer, a mudança é uma linha AQUI e nenhuma linha
 * no controller. Sem a Policy, a regra ficaria espalhada por cinco arquivos, e
 * é assim que se produz um endpoint que esqueceram de proteger.
 *
 * O gancho já existe: a coluna `users.is_admin` (usada nos gates do Pulse e do
 * Horizon, em AppServiceProvider e HorizonServiceProvider). Restringir remoção a
 * administrador, por exemplo, é trocar o `return true` do `delete()` por
 * `return $user->is_admin;` — e o teste que hoje remove veículo com um usuário
 * comum falha, mostrando exatamente quem depende da regra.
 *
 * Não existem `viewAny` nem `view`: leitura de frota é pública neste projeto
 * (ver routes/api.php). Se um dia deixar de ser, adicione os métodos aqui e o
 * gate na rota — não um `if` no controller.
 */
final readonly class VehiclePolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return true;
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return true;
    }
}
