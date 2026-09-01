<?php

declare(strict_types=1);

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\FinancialTransaction;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

beforeEach(function (): void {
    $this->seed(AccessControlSeeder::class);
});

it('limita o desenvolvedor aos projetos em que participa e oculta o orçamento', function (): void {
    $developer = userWithRole('developer');
    $client = Client::query()->create(['name' => 'Cliente A']);
    $assigned = Project::query()->create([
        'client_id' => $client->id,
        'name' => 'Projeto atribuído',
        'budget' => 150000,
    ]);
    $unassigned = Project::query()->create([
        'client_id' => $client->id,
        'name' => 'Projeto restrito',
        'budget' => 35000,
    ]);
    $assigned->members()->attach($developer->id, [
        'project_role' => 'developer',
        'joined_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Sanctum::actingAs($developer);

    getJson('/api/projects')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $assigned->id)
        ->assertJsonMissingPath('data.0.budget');

    getJson("/api/projects/{$unassigned->id}")->assertForbidden();
});

it('aplica negação individual antes das permissões herdadas da role', function (): void {
    $developer = userWithRole('developer');
    $permission = Permission::query()->where('name', 'project.view_assigned')->sole();
    $developer->directPermissions()->attach($permission->id, [
        'allowed' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Sanctum::actingAs($developer);

    getJson('/api/projects')->assertForbidden();
});

it('impede desenvolvedor de acessar dados financeiros', function (): void {
    Sanctum::actingAs(userWithRole('developer'));

    getJson('/api/financial-transactions')->assertForbidden();
    getJson('/api/dashboard')
        ->assertOk()
        ->assertJsonPath('data.scope', 'personal')
        ->assertJsonMissingPath('data.financial');
});

it('permite ao desenvolvedor alterar somente o status da própria tarefa', function (): void {
    $developer = userWithRole('developer');
    $client = Client::query()->create(['name' => 'Cliente A']);
    $project = Project::query()->create(['client_id' => $client->id, 'name' => 'Projeto A']);
    $project->members()->attach($developer->id, [
        'project_role' => 'developer',
        'joined_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $task = Task::query()->create([
        'project_id' => $project->id,
        'title' => 'Implementar API',
        'assigned_to' => $developer->id,
    ]);
    Sanctum::actingAs($developer);

    patchJson("/api/tasks/{$task->id}", [
        'title' => 'Título indevido',
        'status' => 'in_progress',
    ])->assertUnprocessable()->assertJsonValidationErrors('title');

    patchJson("/api/tasks/{$task->id}", ['status' => 'in_progress'])
        ->assertOk()
        ->assertJsonPath('data.status', 'in_progress');
});

it('aceita lead público sem confiar em campos administrativos enviados pelo cliente', function (): void {
    $user = User::factory()->create();

    postJson('/api/public/leads', [
        'name' => 'Contato público',
        'email' => 'contato@example.test',
        'objective' => 'Preciso de um sistema web.',
        'status' => 'won',
        'assigned_to' => $user->id,
    ])
        ->assertCreated()
        ->assertJsonPath('data.status', 'new')
        ->assertJsonPath('data.assigned_to', null);
});

it('registra alterações de domínio em auditoria imutável', function (): void {
    $manager = userWithRole('manager');
    $client = Client::query()->create(['name' => 'Cliente A']);
    $project = Project::query()->create(['client_id' => $client->id, 'name' => 'Projeto A']);
    Sanctum::actingAs($manager);

    patchJson("/api/projects/{$project->id}", ['progress' => 40])->assertOk();

    $audit = AuditLog::query()
        ->where('auditable_type', $project->getMorphClass())
        ->where('auditable_id', $project->id)
        ->where('action', 'updated')
        ->latest('id')
        ->sole();

    expect($audit->user_id)->toBe($manager->id)
        ->and($audit->new_values)->toMatchArray(['progress' => 40]);

    getJson('/api/audit-logs')->assertOk();
});

it('permite ao financeiro registrar transações sem expor a operação ao dev', function (): void {
    $finance = userWithRole('finance');
    Sanctum::actingAs($finance);

    postJson('/api/financial-transactions', [
        'type' => 'income',
        'description' => 'Parcela do projeto',
        'amount' => 5000,
        'due_date' => now()->addWeek()->toDateString(),
    ])
        ->assertCreated()
        ->assertJsonPath('data.amount', '5000.00');

    expect(FinancialTransaction::query()->count())->toBe(1);
});

function userWithRole(string $roleName): User
{
    $user = User::factory()->create();
    $role = Role::query()->where('name', $roleName)->sole();
    $user->roles()->attach($role->id, [
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $user;
}
