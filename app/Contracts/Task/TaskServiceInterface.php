<?php

namespace App\Contracts\Task;

use App\DTO\Task\TaskDTO;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskServiceInterface
{
    public function list(): Collection;

    public function get(int $id): Task;

    public function create(TaskDTO $data): Task;

    public function update(int $id, TaskDTO $data): Task;

    public function delete(int $id): void;
}
