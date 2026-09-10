<?php

namespace App\Actions\Support;

use App\Enums\Role;
use App\Enums\SupportTicketStatus;
use App\Models\CustomerUser;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketOpened;
use App\Support\ReferenceGenerator;
use Illuminate\Support\Facades\Notification;

class UpsertSupportTicket
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?SupportTicket $ticket = null, ?CustomerUser $openedBy = null): SupportTicket
    {
        $isNew = $ticket === null;
        $ticket ??= new SupportTicket;

        if ($isNew) {
            $ticket->reference = $this->references->next('TKT');
            $ticket->opened_by_user_id = $openedBy?->id;
        }

        $ticket->fill($data);

        if ($ticket->isDirty('status')) {
            $ticket->resolved_at = $ticket->status->isOpen() ? null : ($ticket->resolved_at ?? now());
        }

        $ticket->save();

        if ($isNew) {
            $recipients = User::query()
                ->whereHas('roles', fn ($q) => $q->whereIn('name', [Role::Support->value, Role::Admin->value]))
                ->get();

            Notification::send($recipients, new SupportTicketOpened($ticket->fresh('customer')));
        }

        return $ticket->refresh();
    }

    public function changeStatus(SupportTicket $ticket, SupportTicketStatus $status): SupportTicket
    {
        $ticket->status = $status;
        $ticket->resolved_at = $status->isOpen() ? null : ($ticket->resolved_at ?? now());
        $ticket->save();

        return $ticket;
    }
}
