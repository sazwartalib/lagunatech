<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadNote>
 */
class LeadNoteFactory extends Factory
{
    protected $model = LeadNote::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'user_id' => User::factory(),
            'body' => fake()->randomElement([
                'Called — no answer, left a voicemail.',
                'Spoke to the client, sending a proposal this week.',
                'Client wants to see similar work we have done.',
                'Budget confirmed. Moving to proposal stage.',
                'Not the right fit for now — following up in Q3.',
            ]),
        ];
    }
}
