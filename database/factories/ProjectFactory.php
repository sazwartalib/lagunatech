<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $start = fake()->dateTimeBetween('-4 months', '+1 month');
        $target = (clone $start)->modify('+'.fake()->numberBetween(30, 150).' days');
        $value = fake()->numberBetween(8, 120) * 1000;

        return [
            'reference' => sprintf('LT-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'name' => fake()->randomElement(['POS System', 'Company Website', 'Mobile App', 'Inventory System', 'Booking Platform', 'CRM Portal', 'E-Commerce Store', 'HR System']),
            'customer_id' => Customer::factory(),
            'lead_id' => User::factory(),
            'customer_pic_name' => fake()->name(),
            'customer_pic_phone' => fake()->numerify('01#-### ####'),
            'type' => fake()->randomElement(['Web Application', 'Mobile Application', 'Custom System', 'Website', 'Technical Service']),
            'technology' => fake()->randomElement(['Laravel + Livewire', 'Laravel + Vue', 'React Native', 'Flutter', 'Next.js', 'WordPress']),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(ProjectStatus::cases()),
            'priority' => fake()->randomElement(Priority::cases()),
            'progress' => fake()->numberBetween(0, 100),
            'start_date' => $start,
            'target_end_date' => $target,
            'actual_end_date' => null,
            'budget' => $value * 0.7,
            'value' => $value,
            'notes' => fake()->boolean(20) ? fake()->sentence(10) : null,
        ];
    }

    public function status(ProjectStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => fake()->randomElement([
                ProjectStatus::Planning,
                ProjectStatus::Development,
                ProjectStatus::Testing,
                ProjectStatus::CustomerReview,
            ]),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => ProjectStatus::Development,
            'target_end_date' => now()->subDays(fake()->numberBetween(3, 30)),
            'progress' => fake()->numberBetween(30, 75),
        ]);
    }
}
