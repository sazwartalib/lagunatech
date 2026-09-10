<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectDeadlineApproaching extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Project $project,
        public readonly bool $overdue,
    ) {}

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
        $due = $this->project->target_end_date;

        return [
            'icon' => $this->overdue ? '🔴' : '⏳',
            'title' => $this->overdue ? 'Project overdue' : 'Project deadline approaching',
            'body' => sprintf(
                '%s (%s) is due %s',
                $this->project->name,
                $this->project->reference,
                $this->overdue ? $due->diffForHumans() : 'on '.$due->format('d M Y'),
            ),
            'url' => route('projects.show', $this->project),
        ];
    }
}
