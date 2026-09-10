<?php

namespace Database\Factories;

use App\Enums\BugStatus;
use App\Enums\Priority;
use App\Models\Bug;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bug>
 */
class BugFactory extends Factory
{
    protected $model = Bug::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $status = fake()->randomElement(BugStatus::cases());

        return [
            'reference' => sprintf('BUG-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'project_id' => Project::factory(),
            'title' => rtrim(fake()->sentence(6), '.'),
            'description' => fake()->paragraphs(2, true),
            'reported_by' => User::factory(),
            'assigned_to' => User::factory(),
            'priority' => fake()->randomElement(Priority::cases()),
            'status' => $status,
            'due_date' => fake()->boolean(60) ? fake()->dateTimeBetween('-1 week', '+3 weeks') : null,
            'resolved_at' => in_array($status, [BugStatus::Fixed, BugStatus::Closed], true) ? now() : null,
        ];
    }

    public function status(BugStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'resolved_at' => in_array($status, [BugStatus::Fixed, BugStatus::Closed], true) ? now() : null,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => BugStatus::Open,
            'priority' => Priority::High,
            'due_date' => now()->subDays(fake()->numberBetween(1, 10)),
            'resolved_at' => null,
        ]);
    }
}
