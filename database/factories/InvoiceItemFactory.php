<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->randomElement([
                'Project deposit (50%)',
                'Development milestone',
                'UI/UX design package',
                'Deployment & handover',
                'Support retainer — 1 month',
                'Additional feature work',
            ]),
            'quantity' => fake()->randomElement([1, 1, 1, 2]),
            'unit_price' => fake()->randomElement([2000, 3500, 5000, 7500, 10000, 15000]),
            'position' => 0,
        ];
    }
}
