<?php

use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tasks', TaskController::class);
