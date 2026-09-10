<?php

namespace App\Livewire\Bugs;

use App\Actions\Bugs\UpsertBug;
use App\Enums\BugStatus;
use App\Enums\Priority;
use App\Models\Bug;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Bugs')]
class BugIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $priority = '';

    #[Url(except: 'false')]
    public bool $mineOnly = false;

    public function mount(): void
    {
        $this->authorize('viewAny', Bug::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'priority', 'mineOnly'], true)) {
            $this->resetPage();
        }
    }

    public function setStatus(int $bugId, string $status, UpsertBug $action): void
    {
        $bug = Bug::findOrFail($bugId);
        $this->authorize('update', $bug);

        $action->changeStatus($bug, BugStatus::from($status));
        $this->dispatch('toast', message: 'Bug status updated.', type: 'info');
    }

    public function render(): View
    {
        $bugs = Bug::query()
            ->with(['project:id,name,reference', 'assignee:id,name'])
            ->when($this->search !== '', fn (Builder $q) => $q->where(function (Builder $inner): void {
                $inner->where('reference', 'like', "%{$this->search}%")
                    ->orWhere('title', 'like', "%{$this->search}%");
            }))
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->priority !== '', fn (Builder $q) => $q->where('priority', $this->priority))
            ->when($this->mineOnly, fn (Builder $q) => $q->where('assigned_to', auth()->id()))
            ->orderByRaw("case status when 'open' then 0 when 'reopened' then 1 when 'in_progress' then 2 when 'testing' then 3 when 'fixed' then 4 else 5 end")
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.bugs.bug-index', [
            'bugs' => $bugs,
            'statuses' => BugStatus::options(),
            'priorities' => Priority::options(),
            'boardStatuses' => BugStatus::board(),
            'openCritical' => Bug::query()->critical()->count(),
        ]);
    }
}
