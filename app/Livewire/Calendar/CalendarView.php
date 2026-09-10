<?php

namespace App\Livewire\Calendar;

use App\Models\Invoice;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Task;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Calendar')]
class CalendarView extends Component
{
    /** First day of the visible month, as Y-m-d. */
    #[Url]
    public string $month;

    /** @var array<string, bool> */
    public array $types = [
        'project' => true,
        'task' => true,
        'milestone' => true,
        'meeting' => true,
        'invoice' => true,
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Project::class);
        $this->month = CarbonImmutable::now()->startOfMonth()->toDateString();
    }

    public function shiftMonth(int $delta): void
    {
        $this->month = CarbonImmutable::parse($this->month)->addMonths($delta)->startOfMonth()->toDateString();
    }

    public function today(): void
    {
        $this->month = CarbonImmutable::now()->startOfMonth()->toDateString();
    }

    /**
     * @return Collection<string, list<array{type: string, label: string, url: string, color: string}>>
     */
    public function events(CarbonImmutable $gridStart, CarbonImmutable $gridEnd): Collection
    {
        $events = collect();
        $range = [$gridStart->toDateString(), $gridEnd->toDateString()];

        if ($this->types['project']) {
            Project::query()->whereBetween('target_end_date', $range)->get(['id', 'name', 'reference', 'target_end_date'])
                ->each(fn (Project $p) => $events->push([
                    'date' => $p->target_end_date->toDateString(),
                    'type' => 'project', 'label' => '🏁 '.$p->name, 'url' => route('projects.show', $p), 'color' => 'brand',
                ]));
        }

        if ($this->types['task']) {
            Task::query()->open()->whereBetween('due_date', $range)->with('project:id')->get(['id', 'title', 'due_date', 'project_id'])
                ->each(fn (Task $t) => $events->push([
                    'date' => $t->due_date->toDateString(),
                    'type' => 'task', 'label' => '✓ '.$t->title, 'url' => route('projects.show', $t->project_id), 'color' => 'slate',
                ]));
        }

        if ($this->types['milestone']) {
            ProjectMilestone::query()->whereBetween('due_date', $range)->get(['id', 'name', 'due_date', 'project_id'])
                ->each(fn (ProjectMilestone $m) => $events->push([
                    'date' => $m->due_date->toDateString(),
                    'type' => 'milestone', 'label' => '◈ '.$m->name, 'url' => route('projects.show', $m->project_id), 'color' => 'purple',
                ]));
        }

        if ($this->types['meeting']) {
            Meeting::query()->whereBetween('scheduled_at', [$gridStart, $gridEnd->endOfDay()])->get(['id', 'title', 'scheduled_at'])
                ->each(fn (Meeting $mt) => $events->push([
                    'date' => $mt->scheduled_at->toDateString(),
                    'type' => 'meeting', 'label' => '🤝 '.$mt->title, 'url' => route('meetings.show', $mt), 'color' => 'blue',
                ]));
        }

        if ($this->types['invoice']) {
            Invoice::query()->open()->whereBetween('due_date', $range)->get(['id', 'reference', 'due_date'])
                ->each(fn (Invoice $inv) => $events->push([
                    'date' => $inv->due_date->toDateString(),
                    'type' => 'invoice', 'label' => '🧾 '.$inv->reference, 'url' => route('invoices.show', $inv), 'color' => 'amber',
                ]));
        }

        return $events->groupBy('date');
    }

    public function render(): View
    {
        $monthStart = CarbonImmutable::parse($this->month)->startOfMonth();
        $gridStart = $monthStart->startOfWeek(CarbonImmutable::SUNDAY);
        $gridEnd = $monthStart->endOfMonth()->endOfWeek(CarbonImmutable::SATURDAY);

        $days = [];
        for ($day = $gridStart; $day->lte($gridEnd); $day = $day->addDay()) {
            $days[] = $day;
        }

        return view('livewire.calendar.calendar-view', [
            'monthStart' => $monthStart,
            'days' => $days,
            'eventsByDate' => $this->events($gridStart, $gridEnd),
        ]);
    }
}
