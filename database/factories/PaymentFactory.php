<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'reference' => sprintf('PAY-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'invoice_id' => Invoice::factory(),
            'recorded_by' => User::factory(),
            'amount' => fake()->randomElement([1000, 2500, 5000, 7500]),
            'paid_on' => fake()->dateTimeBetween('-2 months', 'now'),
            'method' => fake()->randomElement(PaymentMethod::cases()),
            'reference_number' => strtoupper(fake()->bothify('TXN-####-????')),
            'notes' => null,
        ];
    }
}
