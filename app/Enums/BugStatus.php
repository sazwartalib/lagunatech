<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum BugStatus: string
{
    use HasLabel;

    case Open = 'open';
    case InProgress = 'in_progress';
    case Fixed = 'fixed';
    case Testing = 'testing';
    case Closed = 'closed';
    case Reopened = 'reopened';

    public function color(): string
    {
        return match ($this) {
            self::Open => 'red',
            self::InProgress => 'blue',
            self::Fixed => 'teal',
            self::Testing => 'amber',
            self::Closed => 'green',
            self::Reopened => 'purple',
        };
    }

    public function isResolved(): bool
    {
        return in_array($this, [self::Fixed, self::Closed], true);
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Closed], true);
    }

    /**
     * @return list<self>
     */
    public static function board(): array
    {
        return [self::Open, self::InProgress, self::Testing, self::Fixed, self::Closed];
    }
}
