<?php

namespace App\Support;

use App\Models\Sequence;
use Illuminate\Support\Facades\DB;

/**
 * Produces sequential, human-friendly identifiers such as LT-2026-001.
 *
 * Each (prefix, period) pair has one row in `sequences`; the value is bumped
 * inside a transaction with a row-level lock so concurrent requests cannot
 * hand out the same number.
 */
class ReferenceGenerator
{
    public function projectReference(?int $year = null): string
    {
        return $this->next('LT', $year);
    }

    public function customerReference(?int $year = null): string
    {
        return $this->next('CUST', $year);
    }

    public function quotationReference(?int $year = null): string
    {
        return $this->next('QT', $year);
    }

    public function invoiceReference(?int $year = null): string
    {
        return $this->next('INV', $year);
    }

    public function changeRequestReference(?int $year = null): string
    {
        return $this->next('CR', $year);
    }

    public function bugReference(?int $year = null): string
    {
        return $this->next('BUG', $year);
    }

    public function leadReference(?int $year = null): string
    {
        return $this->next('LEAD', $year);
    }

    /**
     * Reserve and format the next value for an arbitrary prefix.
     */
    public function next(string $prefix, ?int $year = null, int $pad = 3): string
    {
        $period = (string) ($year ?? now()->year);

        $value = DB::transaction(function () use ($prefix, $period): int {
            $sequence = Sequence::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    ['prefix' => $prefix, 'period' => $period],
                    ['current_value' => 0],
                );

            $sequence->increment('current_value');

            return $sequence->current_value;
        });

        return sprintf('%s-%s-%s', $prefix, $period, str_pad((string) $value, $pad, '0', STR_PAD_LEFT));
    }
}
