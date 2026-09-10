<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $company = fake()->unique()->company();

        return [
            'reference' => sprintf('CUST-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'company_name' => $company.' '.fake()->randomElement(['Sdn Bhd', 'Enterprise', 'Trading', 'Holdings']),
            'contact_person' => fake()->name(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('01#-### ####'),
            'registration_number' => fake()->numerify('#######-#'),
            'address' => fake()->address(),
            'status' => fake()->randomElement(CustomerStatus::cases()),
            'account_manager_id' => User::factory(),
            'notes' => fake()->boolean(30) ? fake()->sentence(12) : null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => CustomerStatus::Active]);
    }

    public function prospect(): static
    {
        return $this->state(fn () => ['status' => CustomerStatus::Prospect]);
    }
}
