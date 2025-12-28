<?php

namespace App\Providers;

use App\Contracts\ApiResponseServiceInterface;
use App\Contracts\Task\TaskRepositoryInterface;
use App\Contracts\Task\TaskServiceInterface;
use App\Repositories\TaskRepository;
use App\Services\ApiResponseService;
use App\Services\Task\TaskService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ApiResponseServiceInterface::class, ApiResponseService::class);

        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
