<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ClientType;
use App\Enums\LeadStatus;
use App\Enums\ProjectMemberRole;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;

final readonly class ConvertLeadToProject
{
    public function __construct(private ConnectionInterface $connection) {}

    /**
     * @param array<string, mixed> $data
     */
    public function handle(Lead $lead, User $actor, array $data): Project
    {
        return $this->connection->transaction(function () use ($lead, $actor, $data): Project {
            $client = isset($data['client_id'])
                ? Client::query()->whereKey((int) $data['client_id'])->sole()
                : $this->createClient($lead, $actor, $data['client']);

            /** @var array<string, mixed> $projectData */
            $projectData = $data['project'];
            $projectData['client_id'] = $client->id;
            $projectData['lead_id'] = $lead->id;
            $projectData['project_type'] ??= $lead->project_type;

            $project = new Project($projectData);
            $project->setAttribute('created_by', $actor->id);
            $project->save();

            if ($project->responsible_id !== null) {
                $project->members()->syncWithoutDetaching([
                    $project->responsible_id => [
                        'project_role' => ProjectMemberRole::ProductOwner->value,
                        'joined_at' => now(),
                    ],
                ]);
            }

            $lead->setAttribute('client_id', $client->id);
            $lead->setAttribute('status', LeadStatus::Won->value);
            $lead->setAttribute('converted_at', now());
            $lead->save();

            return $project->load(['client', 'members']);
        });
    }

    /** @param array<string, mixed> $data */
    private function createClient(Lead $lead, User $actor, array $data): Client
    {
        $data['type'] = isset($data['company_name'])
            ? ClientType::Company->value
            : ClientType::Individual->value;
        $data['email'] ??= $lead->email;
        $data['phone'] ??= $lead->phone;

        $client = new Client($data);
        $client->setAttribute('created_by', $actor->id);
        $client->save();

        return $client;
    }
}
