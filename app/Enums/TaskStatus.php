<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum TaskStatus: string
{
    use HasLabel;

    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Blocked = 'blocked';
    case Review = 'review';
    case Done = 'done';

    public function color(): string
    {
        return match ($this) {
            self::Todo => 'slate',
            self::InProgress => 'blue',
            self::Blocked => 'red',
            self::Review => 'amber',
            self::Done => 'green',
        };
    }

    public function isComplete(): bool
    {
        return $this === self::Done;
    }

    /**
     * Board column order.
     *
     * @return list<self>
     */
    public static function board(): array
    {
        return [self::Todo, self::InProgress, self::Blocked, self::Review, self::Done];
    }
}
