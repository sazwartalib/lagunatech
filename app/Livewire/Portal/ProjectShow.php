<?php

namespace App\Livewire\Portal;

use App\Enums\ChangeRequestStatus;
use App\Livewire\Portal\Concerns\InteractsWithPortalCustomer;
use App\Models\Project;
use App\Support\ReferenceGenerator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.portal')]
class ProjectShow extends Component
{
    use InteractsWithPortalCustomer;

    public Project $project;

    public bool $showChangeForm = false;

    #[Validate('required|string|max:255')]
    public string $crTitle = '';

    #[Validate('required|string|max:5000')]
    public string $crDescription = '';

    public function mount(Project $project): void
    {
        $this->assertOwned($project->customer_id);
        $this->project = $project;
    }

    public function submitChangeRequest(ReferenceGenerator $references): void
    {
        $this->validate();

        $this->project->changeRequests()->create([
            'reference' => $references->changeRequestReference(),
            'customer_id' => $this->project->customer_id,
            'title' => $this->crTitle,
            'description' => $this->crDescription,
            'status' => ChangeRequestStatus::Pending,
        ]);

        $this->reset('crTitle', 'crDescription', 'showChangeForm');
        $this->dispatch('toast', message: 'Change request submitted. Our team will review and quote it.');
    }

    public function render(): View
    {
        $this->project->loadMissing([
            'lead:id,name',
            'milestones',
            // Only non-internal documents are ever exposed to the portal.
            'documents' => fn ($q) => $q->customerVisible()->latest(),
        ]);

        return view('livewire.portal.project-show', [
            'title' => $this->project->name,
            'changeRequests' => $this->project->changeRequests()->latest()->get(),
        ]);
    }
}
