<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum SupportTicketStatus: string
{
    use HasLabel;

    case Open = 'open';
    case InProgress = 'in_progress';
    case WaitingCustomer = 'waiting_customer';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function color(): string
    {
        return match ($this) {
            self::Open => 'red',
            self::InProgress => 'blue',
            self::WaitingCustomer => 'amber',
            self::Resolved => 'green',
            self::Closed => 'slate',
        };
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Resolved, self::Closed], true);
    }
}
