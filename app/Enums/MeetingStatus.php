<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum MeetingStatus: string
{
    use HasLabel;

    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'blue',
            self::Completed => 'green',
            self::Cancelled => 'slate',
        };
    }
}
