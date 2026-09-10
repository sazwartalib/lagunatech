<?php

namespace Database\Factories;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuotationItem>
 */
class QuotationItemFactory extends Factory
{
    protected $model = QuotationItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quotation_id' => Quotation::factory(),
            'description' => fake()->randomElement([
                'Requirement gathering & solution design',
                'UI/UX design',
                'Backend development',
                'Frontend development',
                'Mobile application build',
                'Testing & QA',
                'Deployment & handover',
                'Training & documentation',
                '3 months support & maintenance',
            ]),
            'quantity' => fake()->randomElement([1, 1, 1, 2, 3]),
            'unit_price' => fake()->randomElement([1500, 2500, 3500, 5000, 8000, 12000]),
            'position' => 0,
        ];
    }
}
