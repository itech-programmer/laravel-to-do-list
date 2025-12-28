<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Done = 'done';

    public static function values(): array
    {
        return array_map(static fn (self $s) => $s->value, self::cases());
    }
}
