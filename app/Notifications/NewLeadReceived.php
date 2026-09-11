<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLeadReceived extends Notification
{
    use Queueable;

    public function __construct(public readonly Lead $lead) {}

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
            'icon' => '✦',
            'title' => 'New enquiry from the website',
            'body' => sprintf(
                '%s%s — %s',
                $this->lead->name,
                $this->lead->company ? ' ('.$this->lead->company.')' : '',
                $this->lead->project_type ?: 'general enquiry',
            ),
            'url' => route('leads.show', $this->lead),
        ];
    }
}
