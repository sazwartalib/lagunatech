<?php

namespace App\Actions\Invoices;

use App\Models\Invoice;
use App\Support\ReferenceGenerator;
use Illuminate\Support\Facades\DB;

class UpsertInvoice
{
    public function __construct(
        private readonly ReferenceGenerator $references,
        private readonly SyncInvoiceState $syncInvoiceState,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array{description: string, quantity: float|string, unit_price: float|string}>  $items
     */
    public function handle(array $data, array $items, ?Invoice $invoice = null): Invoice
    {
        return DB::transaction(function () use ($data, $items, $invoice): Invoice {
            $invoice ??= new Invoice;

            if (! $invoice->exists) {
                $invoice->reference = $this->references->invoiceReference();
                $invoice->created_by = auth()->id();
            }

            $invoice->fill($data)->save();

            $invoice->items()->delete();
            foreach (array_values($items) as $position => $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'position' => $position,
                ]);
            }

            $invoice->load('items', 'payments')->recalculateTotals();

            return $this->syncInvoiceState->handle($invoice);
        });
    }
}
