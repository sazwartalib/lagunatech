<?php

namespace Database\Factories;

use App\Enums\BillingCycle;
use App\Enums\MaintenanceStatus;
use App\Models\Customer;
use App\Models\MaintenancePlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenancePlan>
 */
class MaintenancePlanFactory extends Factory
{
    protected $model = MaintenancePlan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cycle = fake()->randomElement(BillingCycle::cases());
        $start = fake()->dateTimeBetween('-8 months', '-1 month');

        return [
            'customer_id' => Customer::factory(),
            'project_id' => null,
            'name' => fake()->randomElement(['Standard Support', 'Priority Support', 'Hosting & Maintenance', 'SLA — Gold']),
            'description' => fake()->sentence(12),
            'status' => fake()->randomElement([MaintenanceStatus::Active, MaintenanceStatus::Active, MaintenanceStatus::Paused, MaintenanceStatus::Ended]),
            'billing_cycle' => $cycle,
            'fee' => fake()->randomElement([300, 500, 800, 1200, 2500]),
            'starts_on' => $start,
            'ends_on' => fake()->boolean(30) ? (clone $start)->modify('+1 year') : null,
            'next_renewal_on' => $cycle->months() ? now()->addDays(fake()->numberBetween(-10, 60)) : null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => MaintenanceStatus::Active]);
    }
}
