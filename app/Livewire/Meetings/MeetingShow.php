<?php

namespace App\Livewire\Meetings;

use App\Actions\Meetings\ConvertActionItemToTask;
use App\Enums\MeetingStatus;
use App\Models\Meeting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MeetingShow extends Component
{
    public Meeting $meeting;

    public string $notes = '';

    public function mount(Meeting $meeting): void
    {
        $this->authorize('view', $meeting);
        $this->meeting = $meeting;
        $this->notes = (string) $meeting->notes;
    }

    public function complete(): void
    {
        $this->authorize('update', $this->meeting);
        $this->meeting->update(['status' => MeetingStatus::Completed, 'notes' => $this->notes ?: null]);
        $this->meeting->refresh();
        $this->dispatch('toast', message: 'Meeting marked complete.');
    }

    public function saveNotes(): void
    {
        $this->authorize('update', $this->meeting);
        $this->meeting->update(['notes' => $this->notes ?: null]);
        $this->dispatch('toast', message: 'Notes saved.');
    }

    public function toggleActionItem(int $id): void
    {
        $this->authorize('update', $this->meeting);
        $item = $this->meeting->actionItems()->findOrFail($id);
        $item->update(['is_done' => ! $item->is_done]);
        $this->meeting->refresh();
    }

    public function convertActionItem(int $id, ConvertActionItemToTask $action): void
    {
        $this->authorize('update', $this->meeting);
        $item = $this->meeting->actionItems()->findOrFail($id);

        $action->handle($item);
        $this->meeting->refresh();
        $this->dispatch('toast', message: 'Action item converted to a task.');
    }

    public function render(): View
    {
        $this->meeting->loadMissing([
            'project:id,name,reference', 'customer:id,company_name', 'organizer:id,name',
            'participants.user:id,name', 'actionItems.owner:id,name', 'actionItems.task:id,status',
        ]);

        return view('livewire.meetings.meeting-show', [
            'title' => $this->meeting->title,
        ]);
    }
}
