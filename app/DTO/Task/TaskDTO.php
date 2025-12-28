<?php

namespace App\DTO\Task;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;

class TaskDTO
{
    public function __construct(
        public string $title,
        public ?string $description,
        public string $status,
    ) {}

    public static function fromStoreRequest(StoreTaskRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            title: $validated['title'],
            description: $validated['description'] ?? null,
            status: $validated['status'],
        );
    }

    public static function fromUpdateRequest(UpdateTaskRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            title: $validated['title'],
            description: $validated['description'] ?? null,
            status: $validated['status'],
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
