<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectMemberRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class ProjectMemberController extends Controller
{
    public function store(StoreProjectMemberRequest $request, Project $project): ProjectResource
    {
        ProjectMember::query()->updateOrCreate(
            ['project_id' => $project->id, 'user_id' => $request->integer('user_id')],
            [
                'project_role' => $request->validated('project_role'),
                'joined_at' => now(),
                'left_at' => null,
            ],
        );

        return new ProjectResource($project->refresh()->load(['client', 'members']));
    }

    public function destroy(Project $project, User $user): ProjectResource
    {
        Gate::authorize('manageMembers', $project);

        ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->sole()
            ->update(['left_at' => now()]);

        return new ProjectResource($project->refresh()->load(['client', 'members']));
    }
}
