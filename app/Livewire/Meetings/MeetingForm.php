<?php

namespace App\Livewire\Meetings;

use App\Actions\Meetings\UpsertMeeting;
use App\Livewire\Forms\MeetingForm as MeetingFormData;
use App\Models\Customer;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MeetingForm extends Component
{
    public MeetingFormData $form;

    public ?Meeting $meeting = null;

    public function mount(?Meeting $meeting = null): void
    {
        if ($meeting?->exists) {
            $this->authorize('update', $meeting);
            $this->meeting = $meeting;
            $meeting->loadMissing('participants', 'actionItems');
            $this->form->setMeeting($meeting);

            return;
        }

        $this->authorize('create', Meeting::class);
        $this->form->scheduled_at = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');
        $this->form->project_id = request()->integer('project') ?: null;

        if ($this->form->project_id) {
            $this->form->customer_id = Project::find($this->form->project_id)?->customer_id;
        }

        $this->form->participant_ids = [auth()->id()];
    }

    /**
     * Livewire cannot call methods on a nested Form object from the frontend,
     * so the action-item buttons proxy through the component.
     */
    public function addActionItem(): void
    {
        $this->form->addActionItem();
    }

    public function removeActionItem(int $index): void
    {
        $this->form->removeActionItem($index);
    }

    public function save(UpsertMeeting $upsert): void
    {
        ['data' => $data, 'participant_ids' => $participants, 'action_items' => $actionItems] = $this->form->validatedData();

        $meeting = $upsert->handle($data, $participants, $actionItems, $this->meeting);

        session()->flash('status', $this->meeting ? 'Meeting updated.' : 'Meeting scheduled.');
        $this->redirectRoute('meetings.show', $meeting, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.meetings.meeting-form', [
            'title' => $this->meeting ? 'Edit meeting' : 'Schedule a meeting',
            'projects' => Project::query()->orderBy('name')->get(['id', 'name', 'reference', 'customer_id']),
            'customers' => Customer::query()->orderBy('company_name')->get(['id', 'company_name']),
            'staff' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
