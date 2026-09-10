<?php

namespace App\Actions\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;

/**
 * Re-derives an invoice's `amount_paid` and `status` from its payments and
 * due date. Safe to call repeatedly; it never moves a draft or cancelled
 * invoice, and it stamps `paid_at` when the balance first clears.
 */
class SyncInvoiceState
{
    public function handle(Invoice $invoice): Invoice
    {
        $invoice->loadMissing('payments');

        $paid = round((float) $invoice->payments->sum('amount'), 2);
        $total = (float) $invoice->total;

        $invoice->amount_paid = $paid;

        if (! in_array($invoice->status, [InvoiceStatus::Draft, InvoiceStatus::Cancelled], true)) {
            $pastDue = $invoice->due_date?->isPast() ?? false;

            $invoice->status = match (true) {
                $paid >= $total && $total > 0 => InvoiceStatus::Paid,
                $pastDue && $paid < $total => InvoiceStatus::Overdue,
                $paid > 0 => InvoiceStatus::PartiallyPaid,
                default => InvoiceStatus::Sent,
            };

            $invoice->paid_at = $invoice->status === InvoiceStatus::Paid
                ? ($invoice->paid_at ?? now())
                : null;
        }

        $invoice->save();

        return $invoice;
    }
}
