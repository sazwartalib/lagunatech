<?php

namespace Database\Factories;

use App\Enums\QuotationStatus;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quotation>
 */
class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $issue = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'reference' => sprintf('QT-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'customer_id' => Customer::factory(),
            'created_by' => User::factory(),
            'title' => fake()->randomElement(['POS System', 'Company Website', 'Mobile App', 'Inventory System', 'CRM Portal']).' — Proposal',
            'status' => fake()->randomElement(QuotationStatus::cases()),
            'issue_date' => $issue,
            'valid_until' => (clone $issue)->modify('+30 days'),
            'discount_type' => 'amount',
            'discount_value' => fake()->randomElement([0, 0, 500, 1000]),
            'tax_rate' => fake()->randomElement([0, 0, 8]),
            'terms' => '50% deposit upon acceptance, balance on delivery. Prices valid for 30 days.',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Quotation $quotation): void {
            if ($quotation->items()->exists()) {
                return;
            }

            QuotationItemFactory::new()->count(fake()->numberBetween(2, 5))->create([
                'quotation_id' => $quotation->id,
            ]);

            $quotation->load('items')->recalculateTotals();
        });
    }

    public function status(QuotationStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'sent_at' => $status !== QuotationStatus::Draft ? now()->subDays(5) : null,
            'decided_at' => in_array($status, [QuotationStatus::Approved, QuotationStatus::Rejected], true) ? now()->subDay() : null,
        ]);
    }

    public function approved(): static
    {
        return $this->status(QuotationStatus::Approved);
    }
}
