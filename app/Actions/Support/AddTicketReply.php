<?php

namespace App\Actions\Support;

use App\Enums\SupportTicketStatus;
use App\Models\CustomerUser;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;

class AddTicketReply
{
    /**
     * Post a reply. A staff reply that is not internal moves the ticket to
     * "waiting customer"; a customer reply re-opens it to "in progress".
     */
    public function handle(SupportTicket $ticket, string $body, User|CustomerUser $author, bool $internal = false): SupportTicketReply
    {
        $reply = $ticket->replies()->create([
            'staff_id' => $author instanceof User ? $author->id : null,
            'customer_user_id' => $author instanceof CustomerUser ? $author->id : null,
            'body' => $body,
            'is_internal' => $author instanceof User ? $internal : false,
        ]);

        if (! $reply->is_internal && $ticket->status->isOpen()) {
            $ticket->update([
                'status' => $author instanceof CustomerUser
                    ? SupportTicketStatus::InProgress
                    : SupportTicketStatus::WaitingCustomer,
            ]);
        }

        return $reply;
    }
}
