<?php

namespace App\Contracts;

use Illuminate\Http\JsonResponse;
interface ApiResponseServiceInterface
{
    public function success(string $message, $data = [], int $status = 200, array $meta = []): JsonResponse;
    public function error(string $message, $data = [], int $status = 400, array $meta = []): JsonResponse;
    public function info(string $message, $data = [], int $status = 200, array $meta = []): JsonResponse;
    public function warning(string $message, $data = [], int $status = 300, array $meta = []): JsonResponse;
    public function validationErrorResponse(array $errors, array $requestData): JsonResponse;
    public function file(string $message, $data = [], int $status = 200): JsonResponse;
    public function paginated(string $message, $paginator, $items): JsonResponse;
}
