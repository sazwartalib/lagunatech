<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::cases());

        return [
            'project_id' => Project::factory(),
            'milestone_id' => null,
            'title' => rtrim(fake()->sentence(4), '.'),
            'description' => fake()->boolean(60) ? fake()->paragraph() : null,
            'assignee_id' => User::factory(),
            'created_by' => User::factory(),
            'status' => $status,
            'priority' => fake()->randomElement(Priority::cases()),
            'due_date' => fake()->boolean(70) ? fake()->dateTimeBetween('-2 weeks', '+6 weeks') : null,
            'estimated_hours' => fake()->randomElement([2, 4, 8, 16, 24, 40]),
            'actual_hours' => $status === TaskStatus::Done ? fake()->randomElement([2, 4, 8, 16, 20]) : null,
            'tags' => fake()->boolean(40) ? fake()->randomElements(['frontend', 'backend', 'bug', 'design', 'devops', 'urgent'], fake()->numberBetween(1, 2)) : null,
            'position' => 0,
            'completed_at' => $status === TaskStatus::Done ? now() : null,
        ];
    }

    public function status(TaskStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'completed_at' => $status === TaskStatus::Done ? now() : null,
        ]);
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn () => ['assignee_id' => $user->id]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::InProgress,
            'due_date' => now()->subDays(fake()->numberBetween(1, 10)),
            'completed_at' => null,
        ]);
    }
}
