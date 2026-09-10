<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $issue = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'reference' => sprintf('INV-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'customer_id' => Customer::factory(),
            'created_by' => User::factory(),
            'title' => fake()->randomElement(['Deposit', 'Milestone 1', 'Final payment', 'Progress billing']),
            'status' => InvoiceStatus::Sent,
            'issue_date' => $issue,
            'due_date' => (clone $issue)->modify('+30 days'),
            'discount_type' => 'amount',
            'discount_value' => 0,
            'tax_rate' => fake()->randomElement([0, 0, 8]),
            'terms' => 'Payable within 30 days to Laguna Tech Sdn Bhd, Maybank 5123 4567 8901.',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Invoice $invoice): void {
            if ($invoice->items()->exists()) {
                return;
            }

            InvoiceItemFactory::new()->count(fake()->numberBetween(1, 4))->create([
                'invoice_id' => $invoice->id,
            ]);

            $invoice->load('items')->recalculateTotals();
        });
    }

    public function status(InvoiceStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => InvoiceStatus::Sent,
            'issue_date' => now()->subDays(60),
            'due_date' => now()->subDays(30),
        ]);
    }
}
