<?php

namespace Tests\Feature\Api\V1;

use App\Contracts\Task\TaskServiceInterface;
use App\DTO\Task\TaskDTO;
use App\Enums\TaskStatus;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function bindTaskServiceMock($mock): void
    {
        $this->app->instance(TaskServiceInterface::class, $mock);
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
    public function index_returns_list(): void
    {
        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldReceive('list')
            ->once()
            ->andReturn(new Collection([
                $this->makeTask(1),
                $this->makeTask(2, 'Second'),
            ]));

        $this->bindTaskServiceMock($mock);

        $res = $this->getJson('/api/v1/tasks');

        $res->assertOk()
            ->assertJsonStructure([
                'type', 'message', 'data',
            ])
            ->assertJsonPath('type', 'success')
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function store_creates_task_and_returns_201(): void
    {
        $payload = [
            'title' => 'New task',
            'description' => 'Hello',
            'status' => TaskStatus::Pending->value,
        ];

        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($dto) use ($payload) {
                return $dto instanceof TaskDTO
                    && $dto->title === $payload['title']
                    && $dto->description === $payload['description']
                    && $dto->status === $payload['status'];
            }))
            ->andReturn($this->makeTask(10, $payload['title'], $payload['description'], $payload['status']));

        $this->bindTaskServiceMock($mock);

        $res = $this->postJson('/api/v1/tasks', $payload);

        $res->assertStatus(201)
            ->assertJsonPath('type', 'success')
            ->assertJsonPath('data.id', 10)
            ->assertJsonPath('data.title', $payload['title'])
            ->assertJsonPath('data.status', $payload['status']);
    }

    /** @test */
    public function store_returns_422_on_empty_title(): void
    {
        $payload = [
            'title' => '',
            'description' => 'x',
            'status' => TaskStatus::Pending->value,
        ];

        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldNotReceive('create');
        $this->bindTaskServiceMock($mock);

        $res = $this->postJson('/api/v1/tasks', $payload);

        $res->assertStatus(422)
            ->assertJsonPath('type', 'error')
            ->assertJsonStructure(['type', 'message', 'data' => ['errors', 'request']]);
    }

    /** @test */
    public function store_returns_422_on_invalid_status(): void
    {
        $payload = [
            'title' => 'Ok',
            'description' => null,
            'status' => 'invalid',
        ];

        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldNotReceive('create');
        $this->bindTaskServiceMock($mock);

        $res = $this->postJson('/api/v1/tasks', $payload);

        $res->assertStatus(422)
            ->assertJsonPath('type', 'error');
    }

    /** @test */
    public function show_returns_task(): void
    {
        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldReceive('get')
            ->once()
            ->with(5)
            ->andReturn($this->makeTask(5, 'Show'));

        $this->bindTaskServiceMock($mock);

        $res = $this->getJson('/api/v1/tasks/5');

        $res->assertOk()
            ->assertJsonPath('type', 'success')
            ->assertJsonPath('data.id', 5)
            ->assertJsonPath('data.title', 'Show');
    }

    /** @test */
    public function show_returns_404_when_not_found(): void
    {
        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldReceive('get')
            ->once()
            ->with(999)
            ->andThrow(new ModelNotFoundException());

        $this->bindTaskServiceMock($mock);

        $res = $this->getJson('/api/v1/tasks/999');

        $res->assertNotFound();
    }

    /** @test */
    public function update_updates_task(): void
    {
        $payload = [
            'title' => 'Updated',
            'description' => 'Changed',
            'status' => TaskStatus::Done->value,
        ];

        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldReceive('update')
            ->once()
            ->with(7, Mockery::type(TaskDTO::class))
            ->andReturn($this->makeTask(7, $payload['title'], $payload['description'], $payload['status']));

        $this->bindTaskServiceMock($mock);

        $res = $this->putJson('/api/v1/tasks/7', $payload);

        $res->assertOk()
            ->assertJsonPath('type', 'success')
            ->assertJsonPath('data.id', 7)
            ->assertJsonPath('data.status', TaskStatus::Done->value);
    }

    /** @test */
    public function update_returns_422_on_invalid_payload(): void
    {
        $payload = [
            'title' => '',
            'description' => 'x',
            'status' => TaskStatus::Pending->value,
        ];

        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldNotReceive('update');
        $this->bindTaskServiceMock($mock);

        $res = $this->putJson('/api/v1/tasks/1', $payload);

        $res->assertStatus(422)
            ->assertJsonPath('type', 'error');
    }

    /** @test */
    public function update_returns_404_when_not_found(): void
    {
        $payload = [
            'title' => 'Ok',
            'description' => null,
            'status' => TaskStatus::Pending->value,
        ];

        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldReceive('update')
            ->once()
            ->with(999, Mockery::type(TaskDTO::class))
            ->andThrow(new ModelNotFoundException());

        $this->bindTaskServiceMock($mock);

        $res = $this->putJson('/api/v1/tasks/999', $payload);

        $res->assertNotFound();
    }

    /** @test */
    public function destroy_deletes_task(): void
    {
        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldReceive('delete')
            ->once()
            ->with(3)
            ->andReturnNull();

        $this->bindTaskServiceMock($mock);

        $res = $this->deleteJson('/api/v1/tasks/3');

        $res->assertOk()
            ->assertJsonPath('type', 'success')
            ->assertJsonPath('message', 'Task deleted');
    }

    /** @test */
    public function destroy_returns_404_when_not_found(): void
    {
        $mock = Mockery::mock(TaskServiceInterface::class);
        $mock->shouldReceive('delete')
            ->once()
            ->with(999)
            ->andThrow(new ModelNotFoundException());

        $this->bindTaskServiceMock($mock);

        $res = $this->deleteJson('/api/v1/tasks/999');

        $res->assertNotFound();
    }
}
