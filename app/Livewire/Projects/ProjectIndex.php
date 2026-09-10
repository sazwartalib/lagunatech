<?php

namespace App\Livewire\Projects;

use App\Enums\Priority;
use App\Enums\ProjectHealth;
use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Support\ProjectHealthCalculator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Projects')]
class ProjectIndex extends Component
{
    use WithPagination;

    /** Restrict to the current user's projects ("My Projects" route). */
    public bool $mineOnly = false;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $priority = '';

    #[Url(except: '')]
    public string $health = '';

    #[Url(except: 'target_end_date')]
    public string $sort = 'target_end_date';

    #[Url(except: 'asc')]
    public string $direction = 'asc';

    public function mount(bool $mineOnly = false): void
    {
        $this->authorize('viewAny', Project::class);
        $this->mineOnly = $mineOnly;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'priority', 'health'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $column): void
    {
        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sort = $column;
        $this->direction = 'asc';
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status', 'priority', 'health');
        $this->resetPage();
    }

    public function render(): View
    {
        $sortable = ['name', 'reference', 'status', 'priority', 'target_end_date', 'progress', 'created_at'];
        $sort = in_array($this->sort, $sortable, true) ? $this->sort : 'target_end_date';
        $direction = $this->direction === 'desc' ? 'desc' : 'asc';

        $query = Project::query()
            ->search($this->search)
            ->with(['customer:id,company_name', 'lead:id,name'])
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->priority !== '', fn (Builder $q) => $q->where('priority', $this->priority))
            ->when($this->mineOnly, function (Builder $q): void {
                $q->where(function (Builder $inner): void {
                    $inner->where('lead_id', auth()->id())
                        ->orWhereHas('members', fn (Builder $m) => $m->whereKey(auth()->id()));
                });
            });

        // Health is derived, so filter it in PHP on the paginated slice's parent set.
        if ($this->health !== '') {
            $calculator = app(ProjectHealthCalculator::class);
            $ids = (clone $query)->get(['id', 'status', 'progress', 'start_date', 'target_end_date'])
                ->filter(fn (Project $p) => $calculator->for($p)->value === $this->health)
                ->pluck('id');
            $query->whereIn('id', $ids);
        }

        $projects = $query->orderBy($sort, $direction)->orderBy('id')->paginate(12);

        return view('livewire.projects.project-index', [
            'projects' => $projects,
            'statuses' => ProjectStatus::options(),
            'priorities' => Priority::options(),
            'healthOptions' => collect(ProjectHealth::cases())
                ->reject(fn (ProjectHealth $h) => $h === ProjectHealth::Completed)
                ->mapWithKeys(fn (ProjectHealth $h) => [$h->value => $h->label()]),
            'hasFilters' => $this->search !== '' || $this->status !== '' || $this->priority !== '' || $this->health !== '',
        ]);
    }
}
