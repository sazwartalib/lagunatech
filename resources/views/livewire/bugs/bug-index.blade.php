<div>
    <x-ui.page-header title="Bugs" subtitle="Issues across all projects.">
        <x-slot:actions>
            @can('create', App\Models\Bug::class)
                <x-ui.button href="{{ route('bugs.create') }}" icon="＋">Log a bug</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    @if ($openCritical > 0)
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-2.5 text-sm text-red-800 ring-1 ring-inset ring-red-600/20">
            {{ $openCritical }} high-priority {{ Str::plural('bug', $openCritical) }} still open.
        </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <div class="relative min-w-[12rem] flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search reference or title…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
        <select wire:model.live="status" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">Any status</option>
            @foreach ($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
        <select wire:model.live="priority" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">Any priority</option>
            @foreach ($priorities as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" wire:model.live="mineOnly" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600"> Assigned to me
        </label>
    </div>

    <x-ui.card flush>
        <div class="divide-y divide-slate-100">
            @forelse ($bugs as $bug)
                <div class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50" wire:key="bug-{{ $bug->id }}">
                    <a href="{{ route('bugs.show', $bug) }}" wire:navigate class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs tabular-nums text-slate-400">{{ $bug->reference }}</span>
                            <span class="truncate text-sm font-medium text-slate-800">{{ $bug->title }}</span>
                        </div>
                        <p class="truncate text-xs text-slate-400">{{ $bug->project->name }}
                            @if ($bug->due_date) · due <span class="{{ $bug->is_overdue ? 'text-red-600' : '' }}">{{ $bug->due_date->format('d M') }}</span>@endif
                        </p>
                    </a>
                    <x-ui.badge :color="$bug->priority->color()" size="xs">{{ $bug->priority->label() }}</x-ui.badge>
                    @can('update', $bug)
                        <select wire:change="setStatus({{ $bug->id }}, $event.target.value)"
                                class="rounded border-0 bg-slate-50 py-1 pl-2 pr-7 text-xs text-slate-600 ring-1 ring-inset ring-slate-200 focus:ring-brand-500">
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($bug->status->value === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @else
                        <x-ui.badge :color="$bug->status->color()" size="xs">{{ $bug->status->label() }}</x-ui.badge>
                    @endcan
                    @if ($bug->assignee)<x-ui.avatar :name="$bug->assignee->name" size="xs" />@else<span class="size-6"></span>@endif
                </div>
            @empty
                <div class="p-6">
                    <x-ui.empty-state icon="🐞" title="No bugs" description="Nothing matches these filters — or the code is flawless.">
                        @can('create', App\Models\Bug::class)
                            <x-ui.button href="{{ route('bugs.create') }}" icon="＋">Log a bug</x-ui.button>
                        @endcan
                    </x-ui.empty-state>
                </div>
            @endforelse
        </div>
        @if ($bugs->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $bugs->links() }}</div>
        @endif
    </x-ui.card>
</div>
