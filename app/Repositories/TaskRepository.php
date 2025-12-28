<?php

namespace App\Repositories;

use App\Contracts\Task\TaskRepositoryInterface;
use App\DTO\Task\TaskDTO;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(): Collection
    {
        return Task::query()->orderByDesc('id')->get();
    }

    public function findOrFail(int $id): Task
    {
        return Task::query()->findOrFail($id);
    }

    public function create(TaskDTO $data): Task
    {
        return Task::query()->create($data->toArray());
    }

    public function update(Task $task, TaskDTO $data): Task
    {
        $task->fill($data->toArray());
        $task->save();

        return $task->refresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
