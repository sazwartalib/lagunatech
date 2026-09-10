<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingActionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingActionItem>
 */
class MeetingActionItemFactory extends Factory
{
    protected $model = MeetingActionItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'owner_id' => User::factory(),
            'description' => fake()->randomElement([
                'Share the updated timeline with the client',
                'Prepare the UAT environment',
                'Send meeting minutes',
                'Raise a change request for the extra report',
                'Confirm go-live date',
            ]),
            'due_date' => fake()->boolean(70) ? fake()->dateTimeBetween('now', '+2 weeks') : null,
            'is_done' => fake()->boolean(30),
            'task_id' => null,
        ];
    }
}
