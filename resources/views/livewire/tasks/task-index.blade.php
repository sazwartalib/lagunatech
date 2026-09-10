@php use App\Enums\TaskStatus; @endphp

<div>
    <x-ui.page-header title="Tasks" subtitle="Everything assigned across projects." />

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-sm">
            <button wire:click="$set('scope', 'mine')" @class(['rounded-md px-3 py-1 font-medium', 'bg-white shadow-sm text-slate-900' => $scope === 'mine', 'text-slate-500' => $scope !== 'mine'])>My tasks</button>
            <button wire:click="$set('scope', 'all')" @class(['rounded-md px-3 py-1 font-medium', 'bg-white shadow-sm text-slate-900' => $scope === 'all', 'text-slate-500' => $scope !== 'all'])>All</button>
        </div>

        <select wire:model.live="status" class="rounded-lg border-0 py-1.5 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">Any status</option>
            @foreach ($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
        <select wire:model.live="priority" class="rounded-lg border-0 py-1.5 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">Any priority</option>
            @foreach ($priorities as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" wire:model.live="overdueOnly" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
            Overdue only
        </label>
    </div>

    <x-ui.card flush>
        <div class="divide-y divide-slate-100">
            @forelse ($tasks as $task)
                <div class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50" wire:key="t-{{ $task->id }}">
                    <button wire:click="toggleDone({{ $task->id }})" title="Toggle done"
                            class="grid size-5 shrink-0 place-items-center rounded-full border-2 {{ $task->status === TaskStatus::Done ? 'border-green-500 bg-green-500 text-white' : 'border-slate-300 text-transparent hover:border-brand-500' }}">
                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
                    </button>

                    <a href="{{ route('projects.show', $task->project_id) }}" wire:navigate class="min-w-0 flex-1">
                        <p class="truncate text-sm {{ $task->status === TaskStatus::Done ? 'text-slate-400 line-through' : 'text-slate-800' }}">{{ $task->title }}</p>
                        <p class="truncate text-xs text-slate-400">{{ $task->project->reference }} · {{ $task->project->name }}</p>
                    </a>

                    <x-ui.badge :color="$task->priority->color()" size="xs">{{ $task->priority->label() }}</x-ui.badge>
                    <x-ui.badge :color="$task->status->color()" size="xs" dot>{{ $task->status->label() }}</x-ui.badge>

                    @if ($task->due_date)
                        <span class="hidden w-16 shrink-0 text-right text-xs font-medium tabular-nums {{ $task->is_overdue ? 'text-red-600' : 'text-slate-500' }} sm:inline">
                            {{ $task->due_date->format('d M') }}
                        </span>
                    @else
                        <span class="hidden w-16 sm:inline"></span>
                    @endif

                    @if ($task->assignee)
                        <x-ui.avatar :name="$task->assignee->name" size="xs" />
                    @else
                        <span class="size-6 shrink-0"></span>
                    @endif
                </div>
            @empty
                <div class="p-6">
                    <x-ui.empty-state icon="✓" title="No tasks"
                        description="{{ $scope === 'mine' ? 'Nothing is assigned to you with these filters.' : 'No tasks match these filters.' }}" />
                </div>
            @endforelse
        </div>

        @if ($tasks->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $tasks->links() }}</div>
        @endif
    </x-ui.card>
</div>
