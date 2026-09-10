<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum ProjectHealth: string
{
    use HasLabel;

    case OnTrack = 'on_track';
    case AtRisk = 'at_risk';
    case Overdue = 'overdue';
    case Completed = 'completed';

    public function color(): string
    {
        return match ($this) {
            self::OnTrack => 'green',
            self::AtRisk => 'amber',
            self::Overdue => 'red',
            self::Completed => 'slate',
        };
    }

    public function dot(): string
    {
        return match ($this) {
            self::OnTrack => '🟢',
            self::AtRisk => '🟡',
            self::Overdue => '🔴',
            self::Completed => '⚪',
        };
    }
}
