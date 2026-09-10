<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerContact>
 */
class CustomerContactFactory extends Factory
{
    protected $model = CustomerContact::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'name' => fake()->name(),
            'role' => fake()->randomElement(['Owner', 'Director', 'IT Manager', 'Operations Lead', 'Finance']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('01#-### ####'),
            'is_primary' => false,
            'notes' => null,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn () => ['is_primary' => true]);
    }
}
