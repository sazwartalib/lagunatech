<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum LeadStatus: string
{
    use HasLabel;

    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Proposal = 'proposal';
    case Won = 'won';
    case Lost = 'lost';

    public function color(): string
    {
        return match ($this) {
            self::New => 'blue',
            self::Contacted => 'indigo',
            self::Qualified => 'purple',
            self::Proposal => 'amber',
            self::Won => 'green',
            self::Lost => 'slate',
        };
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Won, self::Lost], true);
    }

    /**
     * @return list<self>
     */
    public static function pipeline(): array
    {
        return [self::New, self::Contacted, self::Qualified, self::Proposal, self::Won];
    }
}
