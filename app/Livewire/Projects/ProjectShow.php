<?php

namespace App\Livewire\Projects;

use App\Enums\MilestoneStatus;
use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class ProjectShow extends Component
{
    public Project $project;

    #[Url]
    public string $tab = 'overview';

    // Quick-add task
    public string $newTaskTitle = '';

    public ?int $newTaskAssignee = null;

    public string $newTaskPriority = 'medium';

    public ?string $newTaskDue = null;

    // Quick-add note / milestone
    public string $newNote = '';

    public string $newMilestoneName = '';

    public ?string $newMilestoneDue = null;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
    }

    public function addTask(): void
    {
        $this->authorize('manageTasks', $this->project);

        $validated = $this->validate([
            'newTaskTitle' => ['required', 'string', 'max:255'],
            'newTaskAssignee' => ['nullable', 'exists:users,id'],
            'newTaskPriority' => ['required', 'in:'.implode(',', array_column(Priority::cases(), 'value'))],
            'newTaskDue' => ['nullable', 'date'],
        ]);

        $this->project->tasks()->create([
            'title' => $validated['newTaskTitle'],
            'assignee_id' => $validated['newTaskAssignee'],
            'priority' => $validated['newTaskPriority'],
            'due_date' => $validated['newTaskDue'],
            'status' => TaskStatus::Todo,
            'created_by' => auth()->id(),
        ]);

        $this->reset('newTaskTitle', 'newTaskAssignee', 'newTaskDue');
        $this->newTaskPriority = 'medium';
        $this->dispatch('toast', message: 'Task added.');
    }

    public function moveTask(int $taskId, string $status): void
    {
        $this->authorize('manageTasks', $this->project);

        $status = TaskStatus::tryFrom($status);
        abort_if($status === null, 422);

        $task = $this->project->tasks()->findOrFail($taskId);
        $task->update([
            'status' => $status,
            'completed_at' => $status === TaskStatus::Done ? now() : null,
        ]);

        $this->dispatch('toast', message: 'Task moved to '.$status->label().'.', type: 'info');
    }

    public function addNote(): void
    {
        $this->authorize('update', $this->project);

        $validated = $this->validate(['newNote' => ['required', 'string', 'max:5000']]);

        $this->project->notes()->create([
            'user_id' => auth()->id(),
            'body' => $validated['newNote'],
            'is_internal' => true,
        ]);

        $this->reset('newNote');
        $this->dispatch('toast', message: 'Note added.');
    }

    public function addMilestone(): void
    {
        $this->authorize('update', $this->project);

        $validated = $this->validate([
            'newMilestoneName' => ['required', 'string', 'max:255'],
            'newMilestoneDue' => ['nullable', 'date'],
        ]);

        $this->project->milestones()->create([
            'name' => $validated['newMilestoneName'],
            'due_date' => $validated['newMilestoneDue'],
            'status' => MilestoneStatus::Pending,
            'position' => (int) $this->project->milestones()->max('position') + 1,
        ]);

        $this->reset('newMilestoneName', 'newMilestoneDue');
        $this->dispatch('toast', message: 'Milestone added.');
    }

    public function setMilestoneProgress(int $milestoneId, int $progress): void
    {
        $this->authorize('update', $this->project);

        $progress = max(0, min(100, $progress));
        $milestone = $this->project->milestones()->findOrFail($milestoneId);

        $milestone->update([
            'progress' => $progress,
            'status' => match (true) {
                $progress === 100 => MilestoneStatus::Completed,
                $progress === 0 => MilestoneStatus::Pending,
                default => MilestoneStatus::InProgress,
            },
            'completed_at' => $progress === 100 ? now() : null,
        ]);
    }

    public function render(): View
    {
        $this->project->load([
            'customer:id,company_name,reference',
            'lead:id,name',
            'members:id,name',
            'milestones',
            'tasks' => fn ($q) => $q->with('assignee:id,name')->orderBy('position')->orderByDesc('id'),
            'notes' => fn ($q) => $q->with('user:id,name'),
            'quotations',
            'invoices.payments',
            'changeRequests' => fn ($q) => $q->with('assignee:id,name'),
            'bugs' => fn ($q) => $q->with('assignee:id,name'),
            'meetings',
            'supportTickets' => fn ($q) => $q->with('assignee:id,name'),
            'maintenancePlans',
        ]);

        $tasksByStatus = $this->project->tasks->groupBy(fn (Task $t) => $t->status->value);

        return view('livewire.projects.project-show', [
            'title' => $this->project->name,
            'tasksByStatus' => $tasksByStatus,
            'boardColumns' => TaskStatus::board(),
            'staff' => $this->project->members->isNotEmpty()
                ? $this->project->members
                : User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'activities' => $this->project->activitiesAsSubject()->with('causer:id,name')->latest()->limit(30)->get(),
        ]);
    }
}
