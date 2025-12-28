<?php

use App\Contracts\ApiResponseServiceInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->namespace('App\Http\Controllers\Api\V1')
                ->group(base_path('routes/api/v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Валидация → 422 (единый формат)
        $exceptions->render(function (ValidationException $exception, $request) {
            if (! $request->expectsJson()) {
                return null;
            }
            $api = app(ApiResponseServiceInterface::class);
            return $api->validationErrorResponse(
                $exception->errors(),
                $request->all()
            );
        });

        // Не найдено (Eloquent) → 404
        $exceptions->render(function (ModelNotFoundException $exception, $request) {
            if (! $request->expectsJson()) {
                return null;
            }
            $api = app(ApiResponseServiceInterface::class);
            $model = class_basename($exception->getModel());
            return $api->error(
                "Entity not found",
                ['model' => $model],
                404
            );
        });

        // Не аутентифицирован → 401
        $exceptions->render(function (AuthenticationException $exception, $request) {
            if (! $request->expectsJson()) {
                return null;
            }
            $api = app(ApiResponseServiceInterface::class);
            return $api->error('Unauthenticated', [], 401);
        });

        // Запрещено → 403
        $exceptions->render(function (AuthorizationException $exception, $request) {
            if (! $request->expectsJson()) {
                return null;
            }
            $api = app(ApiResponseServiceInterface::class);
            return $api->error('Forbidden', [], 403);
        });

        // Лимит запросов → 429
        $exceptions->render(function (ThrottleRequestsException $exception, $request) {
            if (! $request->expectsJson()) {
                return null;
            }
            $api = app(ApiResponseServiceInterface::class);
            $retryAfter = (int) ($exception->getHeaders()['Retry-After'] ?? 0);
            return $api->error('Too Many Requests', [
                'retry_after' => $retryAfter,
            ], 429);
        });

        // Любой HttpException (404/405/409/500… с кастомным статусом)
        $exceptions->render(function (HttpExceptionInterface $exception, $request) {
            if (! $request->expectsJson()) {
                return null;
            }
            $api = app(ApiResponseServiceInterface::class);
            $status  = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'HTTP Error';
            return $api->error($message, [], $status);
        });

        // Фоллбек: неожиданные ошибки → 500
        $exceptions->render(function (Throwable $exception, $request) {
            if (! $request->expectsJson()) {
                return null;
            }
            $api = app(ApiResponseServiceInterface::class);
            $payload = app()->hasDebugModeEnabled()
                ? [
                    'exception' => get_class($exception),
                    'message'   => $exception->getMessage(),
                ]
                : [];
            return $api->error('Server error', $payload, 500);
        });
    })->create();
