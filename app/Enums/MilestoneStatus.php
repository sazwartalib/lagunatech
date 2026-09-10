<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum MilestoneStatus: string
{
    use HasLabel;

    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'slate',
            self::InProgress => 'blue',
            self::Completed => 'green',
        };
    }
}
