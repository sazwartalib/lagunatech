<?php

namespace Database\Factories;

use App\Enums\CommunicationType;
use App\Models\Communication;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Communication>
 */
class CommunicationFactory extends Factory
{
    protected $model = Communication::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $needsFollowUp = fake()->boolean(35);

        return [
            'customer_id' => Customer::factory(),
            'project_id' => null,
            'user_id' => User::factory(),
            'type' => fake()->randomElement(CommunicationType::cases()),
            'communicated_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'summary' => fake()->randomElement([
                'Customer requested an update on the deployment timeline.',
                'Discussed the new WhatsApp notification feature — will raise a change request.',
                'Confirmed bank details for the deposit invoice.',
                'Walked the client through the staging build; feedback to follow.',
                'Client reported a login issue; logged as a bug.',
                'Agreed to a review meeting next week.',
            ]),
            'action_required' => $needsFollowUp ? fake()->randomElement([
                'Send revised quotation',
                'Create change request',
                'Follow up on outstanding invoice',
                'Schedule review meeting',
            ]) : null,
            'follow_up_on' => $needsFollowUp ? fake()->dateTimeBetween('now', '+2 weeks') : null,
            'follow_up_done' => false,
        ];
    }
}
