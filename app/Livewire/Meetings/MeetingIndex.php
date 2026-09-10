<?php

namespace App\Livewire\Meetings;

use App\Models\Meeting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Meetings')]
class MeetingIndex extends Component
{
    use WithPagination;

    #[Url(except: 'upcoming')]
    public string $filter = 'upcoming'; // upcoming | past | all

    public function mount(): void
    {
        $this->authorize('viewAny', Meeting::class);
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $meetings = Meeting::query()
            ->with(['project:id,name', 'customer:id,company_name', 'organizer:id,name'])
            ->withCount('actionItems')
            ->when($this->filter === 'upcoming', fn (Builder $q) => $q->where('scheduled_at', '>=', now()->startOfDay())->orderBy('scheduled_at'))
            ->when($this->filter === 'past', fn (Builder $q) => $q->where('scheduled_at', '<', now()->startOfDay())->latest('scheduled_at'))
            ->when($this->filter === 'all', fn (Builder $q) => $q->latest('scheduled_at'))
            ->paginate(15);

        return view('livewire.meetings.meeting-index', [
            'meetings' => $meetings,
            'upcomingCount' => Meeting::query()->upcoming()->count(),
        ]);
    }
}
