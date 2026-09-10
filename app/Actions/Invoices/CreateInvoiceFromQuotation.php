<?php

namespace App\Actions\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Support\ReferenceGenerator;
use Illuminate\Support\Facades\DB;

/**
 * Drafts an invoice that mirrors a quotation's line items and adjustments,
 * so an approved quote can be billed in one click.
 */
class CreateInvoiceFromQuotation
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    public function handle(Quotation $quotation, ?int $dueInDays = 30): Invoice
    {
        return DB::transaction(function () use ($quotation, $dueInDays): Invoice {
            $invoice = Invoice::create([
                'reference' => $this->references->invoiceReference(),
                'customer_id' => $quotation->customer_id,
                'project_id' => $quotation->project_id,
                'quotation_id' => $quotation->id,
                'created_by' => auth()->id(),
                'title' => $quotation->title,
                'status' => InvoiceStatus::Draft,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays($dueInDays)->toDateString(),
                'discount_type' => $quotation->discount_type,
                'discount_value' => $quotation->discount_value,
                'tax_rate' => $quotation->tax_rate,
                'terms' => $quotation->terms,
            ]);

            foreach ($quotation->items as $position => $item) {
                $invoice->items()->create([
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'position' => $position,
                ]);
            }

            return $invoice->load('items')->recalculateTotals();
        });
    }
}
