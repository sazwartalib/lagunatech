<?php

namespace App\Livewire\Portal;

use App\Actions\Support\AddTicketReply;
use App\Actions\Support\UpsertSupportTicket;
use App\Livewire\Portal\Concerns\InteractsWithPortalCustomer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Support')]
class Tickets extends Component
{
    use InteractsWithPortalCustomer;

    public ?int $selectedId = null;

    public bool $showForm = false;

    // New ticket
    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|max:10000')]
    public string $body = '';

    #[Validate('required|in:low,medium,high,urgent')]
    public string $priority = 'medium';

    public ?int $project_id = null;

    // Reply
    #[Validate('required|string|max:5000')]
    public string $reply = '';

    public function create(UpsertSupportTicket $upsert): void
    {
        $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
        ]);

        $projectId = $this->project_id
            && $this->customer()->projects()->whereKey($this->project_id)->exists()
                ? $this->project_id
                : null;

        $ticket = $upsert->handle([
            'customer_id' => $this->customer()->id,
            'project_id' => $projectId,
            'subject' => $this->subject,
            'description' => $this->body,
            'priority' => $this->priority,
        ], openedBy: $this->portalUser());

        $this->reset('subject', 'body', 'project_id', 'showForm');
        $this->priority = 'medium';
        $this->selectedId = $ticket->id;
        $this->dispatch('toast', message: "Ticket {$ticket->reference} submitted.");
    }

    public function select(int $id): void
    {
        $this->customer()->supportTickets()->findOrFail($id);
        $this->selectedId = $id;
    }

    public function postReply(AddTicketReply $action): void
    {
        $this->validate(['reply' => ['required', 'string', 'max:5000']]);

        $ticket = $this->customer()->supportTickets()->findOrFail($this->selectedId);
        $action->handle($ticket, $this->reply, $this->portalUser());

        $this->reset('reply');
        $this->dispatch('toast', message: 'Reply sent.');
    }

    public function render(): View
    {
        $tickets = $this->customer()->supportTickets()->latest()->get();

        $selected = $this->selectedId
            ? $this->customer()->supportTickets()
                ->with(['replies' => fn ($q) => $q->where('is_internal', false)->with(['staff:id,name', 'customerUser:id,name'])])
                ->find($this->selectedId)
            : null;

        return view('livewire.portal.tickets', [
            'tickets' => $tickets,
            'selected' => $selected,
            'projects' => $this->customer()->projects()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
