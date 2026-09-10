<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum QuotationStatus: string
{
    use HasLabel;

    case Draft = 'draft';
    case Sent = 'sent';
    case Viewed = 'viewed';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Expired = 'expired';

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'slate',
            self::Sent => 'blue',
            self::Viewed => 'indigo',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::Expired => 'amber',
        };
    }

    /**
     * A quotation can still be edited only before it leaves the building.
     */
    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Sent], true);
    }

    public function isDecided(): bool
    {
        return in_array($this, [self::Approved, self::Rejected], true);
    }
}
