<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum CustomerStatus: string
{
    use HasLabel;

    case Prospect = 'prospect';
    case Active = 'active';
    case Inactive = 'inactive';

    public function color(): string
    {
        return match ($this) {
            self::Prospect => 'purple',
            self::Active => 'green',
            self::Inactive => 'slate',
        };
    }
}
