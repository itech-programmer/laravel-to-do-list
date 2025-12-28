<?php

namespace App\Services\Task;

use App\Contracts\Task\TaskRepositoryInterface;
use App\Contracts\Task\TaskServiceInterface;
use App\DTO\Task\TaskDTO;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskService implements TaskServiceInterface
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function list(): Collection
    {
        return $this->tasks->all();
    }

    public function get(int $id): Task
    {
        return $this->tasks->findOrFail($id);
    }

    public function create(TaskDTO $data): Task
    {
        return $this->tasks->create($data);
    }

    public function update(int $id, TaskDTO $data): Task
    {
        $task = $this->tasks->findOrFail($id);

        return $this->tasks->update($task, $data);
    }

    public function delete(int $id): void
    {
        $task = $this->tasks->findOrFail($id);

        $this->tasks->delete($task);
    }
}
