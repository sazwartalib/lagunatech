<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum MaintenanceStatus: string
{
    use HasLabel;

    case Active = 'active';
    case Paused = 'paused';
    case Ended = 'ended';

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Paused => 'amber',
            self::Ended => 'slate',
        };
    }
}
