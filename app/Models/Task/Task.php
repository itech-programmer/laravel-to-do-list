<?php

namespace App\Models\Task;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
    ];
}
