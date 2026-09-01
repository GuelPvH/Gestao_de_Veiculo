<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ListTasks;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class TaskController extends Controller
{
    public function index(ListTasksRequest $request, ListTasks $listTasks): AnonymousResourceCollection
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        return TaskResource::collection(
            $listTasks->handle($user, $request->validated()),
        );
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = new Task($request->validated());
        $task->setAttribute('created_by', $request->user()?->getAuthIdentifier());
        $task->save();

        return (new TaskResource($task->load('assignee')))->response()->setStatusCode(201);
    }

    public function show(Task $task): TaskResource
    {
        Gate::authorize('view', $task);

        return new TaskResource($task->load(['assignee', 'comments.user']));
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $task->update($request->validated());

        return new TaskResource($task->refresh()->load('assignee'));
    }

    public function destroy(Task $task): Response
    {
        Gate::authorize('delete', $task);
        $task->delete();

        return response()->noContent();
    }
}
