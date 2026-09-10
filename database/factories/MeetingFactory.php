<?php

namespace Database\Factories;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meeting>
 */
class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $when = fake()->dateTimeBetween('-1 month', '+3 weeks');
        $isPast = $when < now();

        return [
            'project_id' => null,
            'customer_id' => null,
            'created_by' => User::factory(),
            'title' => fake()->randomElement([
                'Kick-off meeting',
                'Requirement walkthrough',
                'Sprint review',
                'UAT session',
                'Deployment planning',
                'Monthly check-in',
            ]),
            'scheduled_at' => $when,
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'location' => fake()->randomElement(['Google Meet', 'Zoom', 'Client office', 'Laguna Tech HQ', 'Phone call']),
            'agenda' => fake()->paragraph(),
            'notes' => $isPast ? fake()->paragraph() : null,
            'status' => $isPast ? MeetingStatus::Completed : MeetingStatus::Scheduled,
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn () => [
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+3 weeks'),
            'status' => MeetingStatus::Scheduled,
            'notes' => null,
        ]);
    }
}
