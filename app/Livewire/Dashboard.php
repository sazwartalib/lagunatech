<?php

namespace App\Livewire;

use App\Enums\ProjectHealth;
use App\Enums\TaskStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * @return array<string, mixed>
     */
    public function stats(): array
    {
        $activeProjects = Project::query()->active()->count();
        $dueSoon = Project::query()->dueWithin(7)->count();
        $overdue = Project::query()->overdue()->count();

        return [
            'active_projects' => $activeProjects,
            'due_soon' => $dueSoon,
            'overdue' => $overdue,
            'open_tasks' => Task::query()->open()->count(),
            'pending_quotations' => Quotation::query()->pending()->count(),
            'outstanding' => (float) Invoice::query()->open()->sum(DB::raw('total - amount_paid')),
            'payments_this_month' => (float) Payment::query()->inMonth()->sum('amount'),
            'overdue_invoices' => Invoice::query()->overdue()->count(),
        ];
    }

    /**
     * @return Collection<int, Task>
     */
    public function myTasks(): Collection
    {
        return Task::query()
            ->with('project:id,name,reference')
            ->where('assignee_id', auth()->id())
            ->whereNot('status', TaskStatus::Done->value)
            ->orderByRaw('due_date is null, due_date asc')
            ->limit(8)
            ->get();
    }

    /**
     * @return array<string, int>
     */
    public function health(): array
    {
        $projects = Project::query()->active()->get(['id', 'status', 'progress', 'start_date', 'target_end_date']);

        return [
            ProjectHealth::OnTrack->value => $projects->where('health', ProjectHealth::OnTrack)->count(),
            ProjectHealth::AtRisk->value => $projects->where('health', ProjectHealth::AtRisk)->count(),
            ProjectHealth::Overdue->value => $projects->where('health', ProjectHealth::Overdue)->count(),
        ];
    }

    /**
     * @return Collection<int, Project>
     */
    public function upcomingDeadlines(): Collection
    {
        return Project::query()
            ->with('customer:id,company_name')
            ->active()
            ->whereNotNull('target_end_date')
            ->whereDate('target_end_date', '>=', now())
            ->orderBy('target_end_date')
            ->limit(6)
            ->get();
    }

    /**
     * @return Collection<int, Activity>
     */
    public function recentActivity(): Collection
    {
        return Activity::query()
            ->with('causer:id,name')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function greeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };
    }

    public function render(): View
    {
        return view('livewire.dashboard', [
            'stats' => $this->stats(),
            'myTasks' => $this->myTasks(),
            'health' => $this->health(),
            'deadlines' => $this->upcomingDeadlines(),
            'activity' => $this->recentActivity(),
            'greeting' => $this->greeting(),
        ]);
    }
}
