<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

final class AccessControlSeeder extends Seeder
{
    /** @var array<string, list<string>> */
    private const ROLE_PERMISSIONS = [
        'manager' => [
            'dashboard.view_general', 'lead.view_any', 'lead.view', 'lead.create',
            'lead.update', 'lead.convert', 'client.view_any', 'client.view',
            'client.create', 'client.update', 'proposal.view_any', 'proposal.view',
            'proposal.create', 'proposal.update', 'proposal.approve', 'project.view_all',
            'project.create', 'project.update', 'project.manage_members',
            'project.view_financial', 'task.view_all', 'task.create', 'task.update',
            'task.update_status', 'task.assign', 'task.comment', 'service.view_any',
            'service.view', 'service.create', 'service.update', 'service.publish',
            'finance.view_any', 'finance.view', 'user.view_any', 'user.view',
            'user.create', 'user.update', 'user.deactivate', 'audit.view',
            'security.view', 'report.general', 'settings.manage',
        ],
        'product_owner' => [
            'dashboard.view_general', 'lead.view_any', 'lead.view', 'lead.create',
            'lead.update', 'lead.convert', 'client.view_any', 'client.view',
            'client.create', 'client.update', 'proposal.view_any', 'proposal.view',
            'proposal.create', 'proposal.update', 'project.view_all', 'project.create',
            'project.update', 'project.manage_members', 'task.view_all', 'task.create',
            'task.update', 'task.update_status', 'task.assign', 'task.comment',
            'service.view_any', 'service.view', 'report.general',
        ],
        'developer' => [
            'dashboard.view_personal', 'client.view', 'project.view_assigned',
            'task.view_assigned', 'task.update_status', 'task.comment',
            'service.view_any', 'service.view',
        ],
        'finance' => [
            'dashboard.view_financial', 'lead.view', 'client.view_any', 'client.view',
            'client.create', 'client.update', 'project.view_all',
            'project.view_financial', 'service.view_any', 'service.view',
            'finance.view_any', 'finance.view', 'finance.create', 'finance.update',
            'finance.approve', 'report.finance',
        ],
    ];

    public function run(): void
    {
        $permissions = collect($this->permissions())->mapWithKeys(
            function (array $definition, string $name): array {
                [$module, $action] = explode('.', $name, 2);

                $permission = Permission::updateOrCreate(
                    ['name' => $name],
                    [
                        'module' => $module,
                        'action' => $action,
                        'description' => $definition['description'],
                    ],
                );

                return [$name => $permission];
            },
        );

        $roles = [
            'super_admin' => ['Super Admin', 'Controle integral e excepcional do sistema.'],
            'manager' => ['Gestor', 'Gestão operacional da software house.'],
            'product_owner' => ['Product Owner', 'Gestão de produto, projetos e backlog.'],
            'developer' => ['Desenvolvedor', 'Execução dos projetos dos quais participa.'],
            'finance' => ['Financeiro', 'Gestão de receitas, despesas e cobranças.'],
        ];

        foreach ($roles as $name => [$displayName, $description]) {
            $role = Role::updateOrCreate(
                ['name' => $name],
                ['display_name' => $displayName, 'description' => $description],
            );
            $role->is_system = true;
            $role->save();

            $permissionNames = $name === 'super_admin'
                ? $permissions->keys()->all()
                : self::ROLE_PERMISSIONS[$name];

            $role->permissions()->sync(
                $permissions->only($permissionNames)->pluck('id')->all(),
            );
        }
    }

    /** @return array<string, array{description: string}> */
    private function permissions(): array
    {
        $names = [
            'dashboard.view_general', 'dashboard.view_personal', 'dashboard.view_financial',
            'lead.view_any', 'lead.view', 'lead.create', 'lead.update', 'lead.delete', 'lead.convert',
            'client.view_any', 'client.view', 'client.create', 'client.update', 'client.delete',
            'proposal.view_any', 'proposal.view', 'proposal.create', 'proposal.update', 'proposal.delete', 'proposal.approve',
            'project.view_all', 'project.view_assigned', 'project.create', 'project.update', 'project.delete',
            'project.manage_members', 'project.view_financial',
            'task.view_all', 'task.view_assigned', 'task.create', 'task.update',
            'task.update_status', 'task.delete', 'task.assign', 'task.comment',
            'service.view_any', 'service.view', 'service.create', 'service.update', 'service.delete', 'service.publish',
            'finance.view_any', 'finance.view', 'finance.create', 'finance.update', 'finance.delete', 'finance.approve',
            'user.view_any', 'user.view', 'user.create', 'user.update', 'user.deactivate', 'user.delete',
            'role.manage', 'audit.view', 'security.view', 'report.general', 'report.finance', 'settings.manage',
        ];

        return collect($names)->mapWithKeys(fn (string $name): array => [
            $name => ['description' => "Permissão {$name} do sistema Deploy."],
        ])->all();
    }
}
