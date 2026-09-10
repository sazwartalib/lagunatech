<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum InvoiceStatus: string
{
    use HasLabel;

    case Draft = 'draft';
    case Sent = 'sent';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'slate',
            self::Sent => 'blue',
            self::PartiallyPaid => 'amber',
            self::Paid => 'green',
            self::Overdue => 'red',
            self::Cancelled => 'slate',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Sent], true);
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Paid, self::Cancelled], true);
    }
}
