<?php

namespace Database\Factories;

use App\Enums\ChangeRequestStatus;
use App\Models\ChangeRequest;
use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChangeRequest>
 */
class ChangeRequestFactory extends Factory
{
    protected $model = ChangeRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $project = Project::factory();

        return [
            'reference' => sprintf('CR-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'project_id' => $project,
            'customer_id' => fn (array $attrs) => Project::find($attrs['project_id'])?->customer_id ?? Customer::factory(),
            'requested_by' => User::factory(),
            'assigned_to' => null,
            'title' => fake()->randomElement([
                'Add WhatsApp notifications',
                'Export reports to Excel',
                'Multi-language support',
                'Add a second payment gateway',
                'Bulk import from CSV',
                'Dark mode',
            ]),
            'description' => fake()->paragraph(),
            'estimated_cost' => fake()->randomElement([null, 1500, 2500, 4000, 6000]),
            'additional_days' => fake()->randomElement([null, 2, 3, 5, 10]),
            'status' => fake()->randomElement(ChangeRequestStatus::cases()),
        ];
    }

    public function status(ChangeRequestStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'decided_at' => in_array($status, [ChangeRequestStatus::Approved, ChangeRequestStatus::Rejected], true) ? now() : null,
        ]);
    }

    public function approved(): static
    {
        return $this->status(ChangeRequestStatus::Approved)->state(fn () => [
            'estimated_cost' => fake()->randomElement([2000, 3500, 5000]),
            'additional_days' => fake()->randomElement([3, 5, 7]),
        ]);
    }
}
