<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ListProjects;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListProjectsRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class ProjectController extends Controller
{
    public function index(ListProjectsRequest $request, ListProjects $listProjects): AnonymousResourceCollection
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return ProjectResource::collection(
            $listProjects->handle($user, $request->validated()),
        );
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = new Project($request->validated());
        $project->setAttribute('created_by', $request->user()?->getAuthIdentifier());
        $project->save();

        return (new ProjectResource($project->load(['client', 'members'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Project $project): ProjectResource
    {
        Gate::authorize('view', $project);

        return new ProjectResource($project->load(['client', 'members']));
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $project->update($request->validated());

        return new ProjectResource($project->refresh()->load(['client', 'members']));
    }

    public function destroy(Project $project): Response
    {
        Gate::authorize('delete', $project);
        $project->delete();

        return response()->noContent();
    }
}
