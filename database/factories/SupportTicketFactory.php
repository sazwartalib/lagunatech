<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\SupportTicketStatus;
use App\Models\Customer;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $status = fake()->randomElement(SupportTicketStatus::cases());

        return [
            'reference' => sprintf('TKT-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'customer_id' => Customer::factory(),
            'project_id' => null,
            'opened_by_user_id' => null,
            'assigned_to' => null,
            'subject' => fake()->randomElement([
                'Cannot log in to the system',
                'Report shows wrong totals',
                'Need an extra user account',
                'Page loads slowly in the afternoon',
                'Export button not working',
                'Request training session',
            ]),
            'description' => fake()->paragraphs(2, true),
            'priority' => fake()->randomElement(Priority::cases()),
            'status' => $status,
            'resolved_at' => in_array($status, [SupportTicketStatus::Resolved, SupportTicketStatus::Closed], true) ? now() : null,
        ];
    }

    public function status(SupportTicketStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'resolved_at' => in_array($status, [SupportTicketStatus::Resolved, SupportTicketStatus::Closed], true) ? now() : null,
        ]);
    }
}
