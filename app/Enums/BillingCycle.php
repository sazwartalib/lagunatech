<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum BillingCycle: string
{
    use HasLabel;

    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case Yearly = 'yearly';
    case AdHoc = 'ad_hoc';

    /**
     * Months added to reach the next renewal date. Ad-hoc plans do not renew.
     */
    public function months(): ?int
    {
        return match ($this) {
            self::Monthly => 1,
            self::Quarterly => 3,
            self::Yearly => 12,
            self::AdHoc => null,
        };
    }

    /**
     * Annualised value of a fee billed on this cycle.
     */
    public function annualise(float $fee): float
    {
        return match ($this) {
            self::Monthly => $fee * 12,
            self::Quarterly => $fee * 4,
            self::Yearly => $fee,
            self::AdHoc => 0.0,
        };
    }
}
