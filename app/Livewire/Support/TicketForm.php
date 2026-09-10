<?php

namespace App\Livewire\Support;

use App\Actions\Support\UpsertSupportTicket;
use App\Enums\Priority;
use App\Models\Customer;
use App\Models\Project;
use App\Models\SupportTicket;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class TicketForm extends Component
{
    #[Validate('required|exists:customers,id')]
    public ?int $customer_id = null;

    #[Validate('nullable|exists:projects,id')]
    public ?int $project_id = null;

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|max:10000')]
    public string $description = '';

    public string $priority = 'medium';

    public function mount(): void
    {
        $this->authorize('create', SupportTicket::class);
        $this->customer_id = request()->integer('customer') ?: null;
        $this->project_id = request()->integer('project') ?: null;
    }

    public function save(UpsertSupportTicket $upsert): void
    {
        $this->validate([
            'priority' => ['required', new Enum(Priority::class)],
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
        ]);

        $ticket = $upsert->handle([
            'customer_id' => $this->customer_id,
            'project_id' => $this->project_id ?: null,
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
        ]);

        session()->flash('status', "Ticket {$ticket->reference} created.");
        $this->redirectRoute('support.show', $ticket, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.support.ticket-form', [
            'title' => 'New support ticket',
            'customers' => Customer::query()->orderBy('company_name')->get(['id', 'company_name']),
            'projects' => Project::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
