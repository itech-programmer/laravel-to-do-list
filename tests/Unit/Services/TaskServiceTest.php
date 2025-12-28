<?php

namespace Tests\Unit\Services;

use App\Contracts\Task\TaskRepositoryInterface;
use App\DTO\Task\TaskDTO;
use App\Enums\TaskStatus;
use App\Models\Task\Task;
use App\Services\Task\TaskService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeTask(
        int $id = 1,
        string $title = 'Test',
        ?string $description = 'Desc',
        string $status = 'pending'
    ): Task {
        $task = new Task([
            'title' => $title,
            'description' => $description,
        ]);

        $task->status = TaskStatus::from($status);

        $task->id = $id;
        $task->exists = true;
        $task->setCreatedAt(now());
        $task->setUpdatedAt(now());

        return $task;
    }

    /** @test */
    public function list_calls_repository_all(): void
    {
        $repo = Mockery::mock(TaskRepositoryInterface::class);
        $repo->shouldReceive('all')->once()->andReturn(new Collection([$this->makeTask(1)]));

        $svc = new TaskService($repo);

        $items = $svc->list();

        $this->assertCount(1, $items);
    }

    /** @test */
    public function get_calls_repository_find_or_fail(): void
    {
        $repo = Mockery::mock(TaskRepositoryInterface::class);
        $repo->shouldReceive('findOrFail')->once()->with(5)->andReturn($this->makeTask(5));

        $svc = new TaskService($repo);

        $task = $svc->get(5);

        $this->assertSame(5, $task->id);
    }

    /** @test */
    public function get_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $repo = Mockery::mock(TaskRepositoryInterface::class);
        $repo->shouldReceive('findOrFail')->once()->with(999)->andThrow(new ModelNotFoundException());

        $svc = new TaskService($repo);
        $svc->get(999);
    }

    /** @test */
    public function create_calls_repository_create(): void
    {
        $dto = new TaskDTO('A', null, 'pending');

        $repo = Mockery::mock(TaskRepositoryInterface::class);
        $repo->shouldReceive('create')->once()->with($dto)->andReturn($this->makeTask(10));

        $svc = new TaskService($repo);

        $task = $svc->create($dto);

        $this->assertSame(10, $task->id);
    }

    /** @test */
    public function update_calls_find_then_update(): void
    {
        $dto = new TaskDTO('U', 'd', 'done');
        $task = $this->makeTask(7);

        $repo = Mockery::mock(TaskRepositoryInterface::class);
        $repo->shouldReceive('findOrFail')->once()->with(7)->andReturn($task);
        $repo->shouldReceive('update')->once()->with($task, $dto)->andReturn($task);

        $svc = new TaskService($repo);

        $updated = $svc->update(7, $dto);

        $this->assertSame(7, $updated->id);
    }

    /** @test */
    public function update_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $dto = new TaskDTO('U', null, 'pending');

        $repo = Mockery::mock(TaskRepositoryInterface::class);
        $repo->shouldReceive('findOrFail')->once()->with(999)->andThrow(new ModelNotFoundException());
        $repo->shouldNotReceive('update');

        $svc = new TaskService($repo);
        $svc->update(999, $dto);
    }

    /** @test */
    public function delete_calls_find_then_delete(): void
    {
        $task = $this->makeTask(3);

        $repo = Mockery::mock(TaskRepositoryInterface::class);
        $repo->shouldReceive('findOrFail')->once()->with(3)->andReturn($task);
        $repo->shouldReceive('delete')->once()->with($task)->andReturnNull();

        $svc = new TaskService($repo);

        $svc->delete(3);

        $this->assertTrue(true);
    }

    /** @test */
    public function delete_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $repo = Mockery::mock(TaskRepositoryInterface::class);
        $repo->shouldReceive('findOrFail')->once()->with(999)->andThrow(new ModelNotFoundException());
        $repo->shouldNotReceive('delete');

        $svc = new TaskService($repo);
        $svc->delete(999);
    }
}
