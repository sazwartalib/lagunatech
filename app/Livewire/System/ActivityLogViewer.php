<?php

namespace App\Livewire\System;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.app')]
#[Title('Activity Logs')]
class ActivityLogViewer extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $subjectType = '';

    #[Url(except: '')]
    public string $causerId = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can(Permission::ViewActivityLogs->value), 403);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['subjectType', 'causerId'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $entries = Activity::query()
            ->with('causer:id,name')
            ->when($this->subjectType !== '', fn ($q) => $q->where('subject_type', 'App\\Models\\'.$this->subjectType))
            ->when($this->causerId !== '', fn ($q) => $q->where('causer_id', $this->causerId))
            ->latest()
            ->paginate(30);

        $subjectTypes = Activity::query()
            ->whereNotNull('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->map(fn (string $type) => class_basename($type))
            ->unique()
            ->sort()
            ->values();

        return view('livewire.system.activity-log-viewer', [
            'entries' => $entries,
            'subjectTypes' => $subjectTypes,
            'causers' => User::query()->whereIn('id', Activity::query()->whereNotNull('causer_id')->distinct()->pluck('causer_id'))
                ->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
