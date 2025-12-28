<?php

namespace App\Contracts\Task;

use App\DTO\Task\TaskDTO;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function all(): Collection;

    public function findOrFail(int $id): Task;

    public function create(TaskDTO $data): Task;

    public function update(Task $task, TaskDTO $data): Task;

    public function delete(Task $task): void;
}
