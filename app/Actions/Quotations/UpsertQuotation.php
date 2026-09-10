<?php

namespace App\Actions\Quotations;

use App\Models\Quotation;
use App\Support\ReferenceGenerator;
use Illuminate\Support\Facades\DB;

class UpsertQuotation
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array{description: string, quantity: float|string, unit_price: float|string}>  $items
     */
    public function handle(array $data, array $items, ?Quotation $quotation = null): Quotation
    {
        return DB::transaction(function () use ($data, $items, $quotation): Quotation {
            $quotation ??= new Quotation;

            if (! $quotation->exists) {
                $quotation->reference = $this->references->quotationReference();
                $quotation->created_by = auth()->id();
            }

            $quotation->fill($data)->save();

            $quotation->items()->delete();
            foreach (array_values($items) as $position => $item) {
                $quotation->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'position' => $position,
                ]);
            }

            return $quotation->load('items')->recalculateTotals();
        });
    }
}
