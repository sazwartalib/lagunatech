<div>
    <x-ui.page-header :title="$mineOnly ? 'My Projects' : 'All Projects'"
                      :subtitle="$mineOnly ? 'Projects you lead or are a member of.' : 'Every project across ' . config('app.name') . '.'">
        <x-slot:actions>
            @can('create', App\Models\Project::class)
                <x-ui.button href="{{ route('projects.create') }}" icon="＋">New Project</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center">
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search project, reference, customer…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
        <div class="flex flex-wrap gap-2">
            <select wire:model.live="status" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
                <option value="">All statuses</option>
                @foreach ($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
            </select>
            <select wire:model.live="priority" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
                <option value="">Any priority</option>
                @foreach ($priorities as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
            </select>
            <select wire:model.live="health" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
                <option value="">Any health</option>
                @foreach ($healthOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
            </select>
            @if ($hasFilters)
                <button wire:click="clearFilters" class="px-1 text-sm font-medium text-slate-500 hover:text-slate-800">Clear</button>
            @endif
        </div>
    </div>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <x-ui.sort-header column="name" :sort="$sort" :direction="$direction">Project</x-ui.sort-header>
                        <x-ui.sort-header column="status" :sort="$sort" :direction="$direction">Status</x-ui.sort-header>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Health</th>
                        <x-ui.sort-header column="priority" :sort="$sort" :direction="$direction">Priority</x-ui.sort-header>
                        <x-ui.sort-header column="progress" :sort="$sort" :direction="$direction">Progress</x-ui.sort-header>
                        <x-ui.sort-header column="target_end_date" :sort="$sort" :direction="$direction">Due</x-ui.sort-header>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">PIC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($projects as $project)
                        <tr wire:key="proj-{{ $project->id }}" class="cursor-pointer hover:bg-slate-50"
                            onclick="window.location='{{ route('projects.show', $project) }}'">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800">{{ $project->name }}</p>
                                <p class="text-xs text-slate-400">{{ $project->reference }} · {{ $project->customer->company_name }}</p>
                            </td>
                            <td class="px-4 py-3"><x-ui.badge :color="$project->status->color()" size="xs">{{ $project->status->label() }}</x-ui.badge></td>
                            <td class="px-4 py-3"><x-ui.badge :color="$project->health->color()" size="xs" dot>{{ $project->health->label() }}</x-ui.badge></td>
                            <td class="px-4 py-3"><x-ui.badge :color="$project->priority->color()" size="xs">{{ $project->priority->label() }}</x-ui.badge></td>
                            <td class="px-4 py-3"><div class="w-24"><x-ui.progress :value="$project->progress" show-label size="sm" /></div></td>
                            <td class="px-4 py-3 text-sm tabular-nums {{ $project->is_overdue ? 'font-medium text-red-600' : 'text-slate-600' }}">
                                {{ $project->target_end_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $project->lead?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10">
                                <x-ui.empty-state icon="📁" title="No projects found"
                                    description="{{ $hasFilters ? 'Try adjusting your filters.' : 'Create your first project to start tracking delivery.' }}">
                                    @can('create', App\Models\Project::class)
                                        @unless ($hasFilters)
                                            <x-ui.button href="{{ route('projects.create') }}" icon="＋">New Project</x-ui.button>
                                        @endunless
                                    @endcan
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($projects->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $projects->links() }}</div>
        @endif
    </x-ui.card>
</div>
