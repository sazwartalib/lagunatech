<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicketReply>
 */
class SupportTicketReplyFactory extends Factory
{
    protected $model = SupportTicketReply::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'support_ticket_id' => SupportTicket::factory(),
            'staff_id' => User::factory(),
            'customer_user_id' => null,
            'body' => fake()->sentence(fake()->numberBetween(8, 25)),
            'is_internal' => false,
        ];
    }

    public function internal(): static
    {
        return $this->state(fn () => ['is_internal' => true]);
    }
}
