<?php

namespace App\Livewire\Projects;

use App\Actions\Projects\UpsertProject;
use App\Livewire\Forms\ProjectForm as ProjectFormData;
use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProjectForm extends Component
{
    public ProjectFormData $form;

    public ?Project $project = null;

    public function mount(?Project $project = null): void
    {
        if ($project?->exists) {
            $this->authorize('update', $project);
            $this->project = $project;
            $project->loadMissing('members:id');
            $this->form->setProject($project);

            return;
        }

        $this->authorize('create', Project::class);

        // Smart default: /projects/create?customer=123 preselects the customer.
        $this->form->customer_id = request()->integer('customer') ?: null;
        $this->form->lead_id = auth()->id();
    }

    public function save(UpsertProject $upsert): void
    {
        ['data' => $data, 'member_ids' => $memberIds] = $this->form->validatedData();

        $project = $upsert->handle($data, $this->project, $memberIds);

        session()->flash('status', $this->project ? 'Project updated.' : "Project {$project->reference} created.");

        $this->redirectRoute('projects.show', $project, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.projects.project-form', [
            'title' => $this->project ? 'Edit '.$this->project->name : 'New project',
            'customers' => Customer::query()->orderBy('company_name')->get(['id', 'company_name']),
            'staff' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
