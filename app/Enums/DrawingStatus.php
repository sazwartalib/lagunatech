<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum DrawingStatus: string
{
    use HasLabel;

    case Draft = 'draft';
    case Final = 'final';

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'amber',
            self::Final => 'green',
        };
    }
}
