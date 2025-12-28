<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\ApiResponseServiceInterface;
use App\Contracts\Task\TaskServiceInterface;
use App\DTO\Task\TaskDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskServiceInterface $tasks,
        private readonly ApiResponseServiceInterface $api,
    ) {}

    public function index(): JsonResponse
    {
        $items = TaskResource::collection($this->tasks->list())->resolve();
        return $this->api->success('Tasks list', $items);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->tasks->create(TaskDTO::fromStoreRequest($request));
        return $this->api->success('Task created', new TaskResource($task), Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $task = $this->tasks->get($id);
        return $this->api->success('Task detail', new TaskResource($task));
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = $this->tasks->update($id, TaskDTO::fromUpdateRequest($request));
        return $this->api->success('Task updated', new TaskResource($task));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->tasks->delete($id);
        return $this->api->success('Task deleted', null, Response::HTTP_OK);
    }
}
