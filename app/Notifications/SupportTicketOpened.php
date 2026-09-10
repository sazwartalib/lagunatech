<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupportTicketOpened extends Notification
{
    use Queueable;

    public function __construct(public readonly SupportTicket $ticket) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'icon' => '🎫',
            'title' => 'New support ticket',
            'body' => sprintf('%s — "%s" from %s', $this->ticket->reference, $this->ticket->subject, $this->ticket->customer->company_name),
            'url' => route('support.show', $this->ticket),
        ];
    }
}
