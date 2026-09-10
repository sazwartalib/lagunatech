<?php

namespace App\Livewire\Reports;

use App\Enums\Permission;
use App\Enums\ProjectHealth;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use App\Support\ProjectHealthCalculator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Reports')]
class ReportsOverview extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->can(Permission::ViewReports->value), 403);
    }

    /**
     * @return array<string, int>
     */
    public function projectReport(): array
    {
        $counts = Project::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $calculator = app(ProjectHealthCalculator::class);
        $atRisk = Project::query()->active()->get(['id', 'status', 'progress', 'start_date', 'target_end_date'])
            ->filter(fn (Project $p) => $calculator->for($p) === ProjectHealth::AtRisk)
            ->count();

        return [
            'total' => (int) $counts->sum(),
            'active' => Project::query()->active()->count(),
            'completed' => (int) $counts->get(ProjectStatus::Completed->value, 0),
            'maintenance' => (int) $counts->get(ProjectStatus::Maintenance->value, 0),
            'overdue' => Project::query()->overdue()->count(),
            'at_risk' => $atRisk,
        ];
    }

    /**
     * @return array<string, float>
     */
    public function financialReport(): array
    {
        return [
            'quotation_value' => (float) Quotation::query()->whereIn('status', ['sent', 'viewed', 'approved'])->sum('total'),
            'approved_quotations' => (float) Quotation::query()->where('status', 'approved')->sum('total'),
            'invoiced' => (float) Invoice::query()->whereNot('status', 'cancelled')->sum('total'),
            'paid' => (float) Payment::query()->sum('amount'),
            'outstanding' => (float) Invoice::query()->open()->sum(DB::raw('total - amount_paid')),
            'overdue' => (float) Invoice::query()->overdue()->sum(DB::raw('total - amount_paid')),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function staffReport()
    {
        return User::query()
            ->where('is_active', true)
            ->withCount([
                'ledProjects as active_projects_count' => fn ($q) => $q->whereNotIn('status', [ProjectStatus::Completed->value, ProjectStatus::Cancelled->value]),
                'assignedTasks as done_tasks_count' => fn ($q) => $q->where('status', TaskStatus::Done->value),
                'assignedTasks as open_tasks_count' => fn ($q) => $q->where('status', '!=', TaskStatus::Done->value),
                'assignedTasks as overdue_tasks_count' => fn ($q) => $q->where('status', '!=', TaskStatus::Done->value)
                    ->whereNotNull('due_date')->whereDate('due_date', '<', now()->toDateString()),
            ])
            ->orderByDesc('open_tasks_count')
            ->get()
            ->map(fn (User $u) => [
                'name' => $u->name,
                'position' => $u->position,
                'active_projects' => $u->active_projects_count,
                'done_tasks' => $u->done_tasks_count,
                'open_tasks' => $u->open_tasks_count,
                'overdue_tasks' => $u->overdue_tasks_count,
            ]);
    }

    public function render(): View
    {
        return view('livewire.reports.reports-overview', [
            'projectReport' => $this->projectReport(),
            'financialReport' => $this->financialReport(),
            'staffReport' => $this->staffReport(),
        ]);
    }
}
