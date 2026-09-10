<?php

namespace App\Livewire\Support;

use App\Actions\Support\AddTicketReply;
use App\Actions\Support\UpsertSupportTicket;
use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class TicketShow extends Component
{
    public SupportTicket $ticket;

    #[Validate('required|string|max:5000')]
    public string $reply = '';

    public bool $replyInternal = false;

    public ?int $assignTo = null;

    public function mount(SupportTicket $ticket): void
    {
        $this->authorize('view', $ticket);
        $this->ticket = $ticket;
        $this->assignTo = $ticket->assigned_to;
    }

    public function postReply(AddTicketReply $action): void
    {
        $this->authorize('reply', $this->ticket);
        $this->validate();

        $action->handle($this->ticket, $this->reply, auth()->user(), $this->replyInternal);
        $this->ticket->refresh();
        $this->reset('reply', 'replyInternal');
        $this->dispatch('toast', message: 'Reply posted.');
    }

    public function changeStatus(string $status, UpsertSupportTicket $action): void
    {
        $this->authorize('update', $this->ticket);
        $action->changeStatus($this->ticket, SupportTicketStatus::from($status));
        $this->ticket->refresh();
        $this->dispatch('toast', message: 'Status updated.', type: 'info');
    }

    public function assign(UpsertSupportTicket $action): void
    {
        $this->authorize('update', $this->ticket);
        $this->ticket->update(['assigned_to' => $this->assignTo ?: null]);
        $this->ticket->refresh();
        $this->dispatch('toast', message: 'Ticket reassigned.', type: 'info');
    }

    public function render(): View
    {
        $this->ticket->loadMissing([
            'customer:id,company_name', 'project:id,name,reference', 'assignee:id,name',
            'openedBy:id,name', 'replies.staff:id,name', 'replies.customerUser:id,name',
        ]);

        return view('livewire.support.ticket-show', [
            'title' => $this->ticket->reference,
            'statuses' => SupportTicketStatus::options(),
            'staff' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
