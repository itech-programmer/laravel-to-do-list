<?php

namespace App\Services;

use App\Contracts\ApiResponseServiceInterface;
use App\Enums\ApiResponseType;
use Illuminate\Http\JsonResponse;

class ApiResponseService implements ApiResponseServiceInterface
{
    public function success(string $message, $data = [], int $status = 200, array $meta = []): JsonResponse
    {
        return response()->json([
            'type' => ApiResponseType::Success->value,
            'message' => $message,
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

    public function error(string $message, $data = [], int $status = 400, array $meta = []): JsonResponse
    {
        return response()->json([
            'type' => ApiResponseType::Error->value,
            'message' => $message,
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

    public function info(string $message, $data = [], int $status = 200, array $meta = []): JsonResponse
    {
        return response()->json([
            'type' => ApiResponseType::Info->value,
            'message' => $message,
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

    public function warning(string $message, $data = [], int $status = 300, array $meta = []): JsonResponse
    {
        return response()->json([
            'type' => ApiResponseType::Warning->value,
            'message' => $message,
            'data' => $data,
            'meta' => (object) $meta,
        ], $status);
    }

    public function validationErrorResponse(array $errors, array $requestData): JsonResponse
    {
        $message = collect($errors)->flatten()->implode('; ');
        return response()->json([
            'type' => ApiResponseType::Error->value,
            'message' => $message,
            'data' => ['errors' => $errors, 'request' => $requestData],
            'meta' => (object) [],
        ], 422);
    }

    public function file(string $message, $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'type' => $status >= 400 ? ApiResponseType::Error->value : ApiResponseType::Success->value,
            'message' => $message,
            'data' => $data,
            'meta' => (object) [],
        ], $status);
    }

    public function paginated(string $message, $paginator, $items): JsonResponse
    {
        return response()->json([
            'type' => ApiResponseType::Success->value,
            'message' => $message,
            'data' => $items,
            'pagination' => [
                'total' => $paginator->total(),
                'perPage' => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
            ],
        ]);
    }
}
