<?php

namespace App\Models\Concerns;

use Illuminate\Support\Collection;

/**
 * Shared money maths for quotations and invoices.
 *
 * Both documents carry a collection of line items plus a header-level discount
 * (fixed amount or percentage) and a tax rate. `recalculateTotals()` derives
 * the denormalised `subtotal`, `discount_amount`, `tax_amount` and `total`
 * columns from those inputs and persists them.
 *
 * @property Collection $items
 * @property string $discount_type
 * @property numeric-string $discount_value
 * @property numeric-string $tax_rate
 */
trait CalculatesDocumentTotals
{
    public function recalculateTotals(): static
    {
        $subtotal = $this->items->sum(
            fn ($item) => round((float) $item->quantity * (float) $item->unit_price, 2)
        );

        $discount = $this->discount_type === 'percent'
            ? $subtotal * ((float) $this->discount_value / 100)
            : min((float) $this->discount_value, $subtotal);

        $discount = round(max(0, $discount), 2);
        $taxable = max(0, $subtotal - $discount);
        $tax = round($taxable * ((float) $this->tax_rate / 100), 2);

        $this->forceFill([
            'subtotal' => round($subtotal, 2),
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total' => round($taxable + $tax, 2),
        ]);

        if ($this->exists) {
            $this->save();
        }

        return $this;
    }
}
