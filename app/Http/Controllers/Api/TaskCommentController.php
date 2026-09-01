<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskCommentRequest;
use App\Http\Resources\TaskCommentResource;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\JsonResponse;

final class TaskCommentController extends Controller
{
    public function store(StoreTaskCommentRequest $request, Task $task): JsonResponse
    {
        $comment = new TaskComment($request->validated());
        $comment->setAttribute('task_id', $task->id);
        $comment->setAttribute('user_id', $request->user()?->getAuthIdentifier());
        $comment->save();

        return new TaskCommentResource($comment->load('user'))
            ->response()
            ->setStatusCode(201);
    }
}
