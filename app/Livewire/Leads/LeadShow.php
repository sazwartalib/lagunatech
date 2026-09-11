<?php

namespace App\Livewire\Leads;

use App\Actions\Leads\ConvertLeadToCustomer;
use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class LeadShow extends Component
{
    public Lead $lead;

    #[Validate('required|string|max:3000')]
    public string $note = '';

    public ?int $ownerId = null;

    public function mount(Lead $lead): void
    {
        $this->authorize('view', $lead);
        $this->lead = $lead;
        $this->ownerId = $lead->owner_id;
    }

    public function setStatus(string $status): void
    {
        $this->authorize('update', $this->lead);

        $target = LeadStatus::from($status);
        $this->lead->update([
            'status' => $target,
            'contacted_at' => $target === LeadStatus::Contacted ? ($this->lead->contacted_at ?? now()) : $this->lead->contacted_at,
            'closed_at' => in_array($target, [LeadStatus::Won, LeadStatus::Lost], true) ? now() : null,
        ]);
        $this->lead->refresh();
        $this->dispatch('toast', message: "Lead marked as {$target->label()}.", type: 'info');
    }

    public function assign(): void
    {
        $this->authorize('update', $this->lead);
        $this->lead->update(['owner_id' => $this->ownerId ?: null]);
        $this->lead->refresh();
        $this->dispatch('toast', message: 'Lead reassigned.', type: 'info');
    }

    public function addNote(): void
    {
        $this->authorize('update', $this->lead);
        $this->validate();

        $this->lead->notes()->create(['user_id' => auth()->id(), 'body' => $this->note]);
        $this->reset('note');
        $this->dispatch('toast', message: 'Note added.');
    }

    public function convert(ConvertLeadToCustomer $action): void
    {
        $this->authorize('convert', $this->lead);

        $customer = $action->handle($this->lead);

        session()->flash('status', "Customer {$customer->reference} created from this lead.");
        $this->redirectRoute('customers.show', $customer, navigate: true);
    }

    public function render(): View
    {
        $this->lead->loadMissing(['owner:id,name', 'convertedCustomer:id,reference,company_name', 'notes.user:id,name', 'attachments']);

        return view('livewire.leads.lead-show', [
            'title' => $this->lead->reference,
            'statuses' => LeadStatus::options(),
            'staff' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
