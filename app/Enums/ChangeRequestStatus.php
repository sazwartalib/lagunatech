<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum ChangeRequestStatus: string
{
    use HasLabel;

    case Pending = 'pending';
    case Quoted = 'quoted';
    case WaitingApproval = 'waiting_approval';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Completed = 'completed';

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'slate',
            self::Quoted => 'blue',
            self::WaitingApproval => 'amber',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::Completed => 'teal',
        };
    }

    /**
     * Approved change requests become billable development work.
     */
    public function isApproved(): bool
    {
        return $this === self::Approved;
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::Rejected, self::Completed], true);
    }
}
