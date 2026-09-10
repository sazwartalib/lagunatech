<?php

namespace App\Livewire\Bugs;

use App\Actions\Bugs\UpsertBug;
use App\Livewire\Forms\BugForm as BugFormData;
use App\Models\Bug;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BugForm extends Component
{
    public BugFormData $form;

    public ?Bug $bug = null;

    public function mount(?Bug $bug = null): void
    {
        if ($bug?->exists) {
            $this->authorize('update', $bug);
            $this->bug = $bug;
            $this->form->setBug($bug);

            return;
        }

        $this->authorize('create', Bug::class);
        $this->form->project_id = request()->integer('project') ?: null;
    }

    public function save(UpsertBug $upsert): void
    {
        $bug = $upsert->handle($this->form->validatedData(), $this->bug);

        session()->flash('status', $this->bug ? 'Bug updated.' : "Bug {$bug->reference} logged.");

        $this->redirectRoute('bugs.show', $bug, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.bugs.bug-form', [
            'title' => $this->bug ? 'Edit '.$this->bug->reference : 'Log a bug',
            'projects' => Project::query()->orderBy('name')->get(['id', 'name', 'reference']),
            'staff' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
