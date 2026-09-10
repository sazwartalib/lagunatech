<?php

namespace App\Livewire\Bugs;

use App\Actions\Bugs\UpsertBug;
use App\Enums\BugStatus;
use App\Models\Bug;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class BugShow extends Component
{
    public Bug $bug;

    #[Validate('required|string|max:5000')]
    public string $comment = '';

    public function mount(Bug $bug): void
    {
        $this->authorize('view', $bug);
        $this->bug = $bug;
    }

    public function setStatus(string $status, UpsertBug $action): void
    {
        $this->authorize('update', $this->bug);
        $action->changeStatus($this->bug, BugStatus::from($status));
        $this->bug->refresh();
        $this->dispatch('toast', message: 'Status updated.', type: 'info');
    }

    public function addComment(): void
    {
        $this->authorize('view', $this->bug);
        $this->validate();

        $this->bug->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->comment,
        ]);

        $this->reset('comment');
        $this->dispatch('toast', message: 'Comment added.');
    }

    public function render(): View
    {
        $this->bug->loadMissing([
            'project:id,name,reference', 'reporter:id,name', 'assignee:id,name',
            'comments.user:id,name',
        ]);

        return view('livewire.bugs.bug-show', [
            'title' => $this->bug->reference,
            'boardStatuses' => BugStatus::board(),
        ]);
    }
}
