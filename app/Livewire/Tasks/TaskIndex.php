<?php

namespace App\Livewire\Tasks;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Tasks')]
class TaskIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $scope = 'mine'; // mine | all

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $priority = '';

    #[Url(except: false)]
    public bool $overdueOnly = false;

    public function mount(): void
    {
        $this->authorize('viewAny', Task::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['scope', 'status', 'priority', 'overdueOnly'], true)) {
            $this->resetPage();
        }
    }

    public function toggleDone(int $taskId): void
    {
        $task = Task::query()->with('project')->findOrFail($taskId);
        $this->authorize('update', $task);

        $done = $task->status === TaskStatus::Done;
        $task->update([
            'status' => $done ? TaskStatus::Todo : TaskStatus::Done,
            'completed_at' => $done ? null : now(),
        ]);
    }

    public function render(): View
    {
        $tasks = Task::query()
            ->with(['project:id,name,reference', 'assignee:id,name'])
            ->when($this->scope === 'mine', fn (Builder $q) => $q->where('assignee_id', auth()->id()))
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->priority !== '', fn (Builder $q) => $q->where('priority', $this->priority))
            ->when($this->overdueOnly, fn (Builder $q) => $q->overdue())
            ->orderByRaw('completed_at is not null')
            ->orderByRaw('due_date is null, due_date asc')
            ->orderByDesc('id')
            ->paginate(20);

        return view('livewire.tasks.task-index', [
            'tasks' => $tasks,
            'statuses' => TaskStatus::options(),
            'priorities' => Priority::options(),
        ]);
    }
}
