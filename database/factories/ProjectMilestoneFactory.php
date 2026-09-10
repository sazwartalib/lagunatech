<?php

namespace Database\Factories;

use App\Enums\MilestoneStatus;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectMilestone>
 */
class ProjectMilestoneFactory extends Factory
{
    protected $model = ProjectMilestone::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $progress = fake()->numberBetween(0, 100);

        return [
            'project_id' => Project::factory(),
            'name' => fake()->randomElement(['Requirement', 'UI/UX', 'Development', 'Testing', 'Deployment']),
            'description' => fake()->boolean(40) ? fake()->sentence() : null,
            'status' => match (true) {
                $progress === 100 => MilestoneStatus::Completed,
                $progress === 0 => MilestoneStatus::Pending,
                default => MilestoneStatus::InProgress,
            },
            'progress' => $progress,
            'due_date' => fake()->dateTimeBetween('-1 month', '+3 months'),
            'completed_at' => $progress === 100 ? now() : null,
            'position' => 0,
        ];
    }
}
