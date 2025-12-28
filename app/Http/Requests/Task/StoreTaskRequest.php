<?php

namespace App\Http\Requests\Task;

use App\Contracts\ApiResponseServiceInterface;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(TaskStatus::values())],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $api = app(ApiResponseServiceInterface::class);

        throw new HttpResponseException(
            $api->validationErrorResponse($validator->errors()->toArray(), $this->all())
        );
    }
}
