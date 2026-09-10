<?php

namespace App\Livewire\ChangeRequests;

use App\Actions\ChangeRequests\ConvertChangeRequestToTask;
use App\Enums\ChangeRequestStatus;
use App\Models\ChangeRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ChangeRequestShow extends Component
{
    public ChangeRequest $changeRequest;

    public function mount(ChangeRequest $changeRequest): void
    {
        $this->authorize('view', $changeRequest);
        $this->changeRequest = $changeRequest;
    }

    public function changeStatus(string $status): void
    {
        $target = ChangeRequestStatus::from($status);

        $this->authorize(
            in_array($target, [ChangeRequestStatus::Approved, ChangeRequestStatus::Rejected], true) ? 'decide' : 'update',
            $this->changeRequest,
        );

        $this->changeRequest->update([
            'status' => $target,
            'decided_at' => in_array($target, [ChangeRequestStatus::Approved, ChangeRequestStatus::Rejected], true)
                ? now()
                : $this->changeRequest->decided_at,
        ]);

        $this->changeRequest->refresh();
        $this->dispatch('toast', message: "Marked as {$target->label()}.");
    }

    public function convertToTask(ConvertChangeRequestToTask $action): void
    {
        $this->authorize('convert', $this->changeRequest);

        $task = $action->handle($this->changeRequest);

        session()->flash('status', 'Task created from this change request.');
        $this->redirectRoute('projects.show', ['project' => $task->project_id, 'tab' => 'tasks'], navigate: true);
    }

    public function render(): View
    {
        $this->changeRequest->loadMissing([
            'project:id,name,reference', 'customer:id,company_name',
            'requester:id,name', 'assignee:id,name', 'task:id,title,status',
        ]);

        return view('livewire.change-requests.change-request-show', [
            'title' => $this->changeRequest->reference,
        ]);
    }
}
