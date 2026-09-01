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
 * O gancho de autorização agora é o RBAC usado pela Deploy. Quando esta fatia
 * demonstrativa for removida, sua Policy também sai sem contaminar as regras do
 * domínio novo.
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
