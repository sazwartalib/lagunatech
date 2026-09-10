<?php

namespace App\Livewire\ChangeRequests;

use App\Actions\ChangeRequests\UpsertChangeRequest;
use App\Livewire\Forms\ChangeRequestForm as ChangeRequestFormData;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ChangeRequestForm extends Component
{
    public ChangeRequestFormData $form;

    public ?ChangeRequest $changeRequest = null;

    public function mount(?ChangeRequest $changeRequest = null): void
    {
        if ($changeRequest?->exists) {
            $this->authorize('update', $changeRequest);
            $this->changeRequest = $changeRequest;
            $this->form->setChangeRequest($changeRequest);

            return;
        }

        $this->authorize('create', ChangeRequest::class);
        $this->form->project_id = request()->integer('project') ?: null;
    }

    public function save(UpsertChangeRequest $upsert): void
    {
        $changeRequest = $upsert->handle($this->form->validatedData(), $this->changeRequest);

        session()->flash('status', $this->changeRequest ? 'Change request updated.' : "Change request {$changeRequest->reference} created.");

        $this->redirectRoute('change-requests.show', $changeRequest, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.change-requests.change-request-form', [
            'title' => $this->changeRequest ? 'Edit '.$this->changeRequest->reference : 'New change request',
            'projects' => Project::query()->with('customer:id,company_name')->orderBy('name')->get(['id', 'name', 'reference', 'customer_id']),
            'staff' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
