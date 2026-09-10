@php
    use App\Enums\ProjectHealth;
    use Illuminate\Support\Number;
@endphp

<div>
    <div class="mb-6">
        <h1 class="text-xl font-semibold tracking-tight text-slate-900">
            {{ $greeting }}, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }} 👋
        </h1>
        <p class="mt-1 text-sm text-slate-500">Here's what's happening at {{ config('app.name') }} today.</p>
    </div>

    {{-- Stat row --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <x-ui.stat-card label="Active Projects" :value="$stats['active_projects']" icon="📁" tone="brand"
            :href="route('projects.index')" />
        <x-ui.stat-card label="Due in 7 days" :value="$stats['due_soon']" icon="⏳" tone="amber" />
        <x-ui.stat-card label="Overdue" :value="$stats['overdue']" icon="🔴" tone="red"
            :hint="$stats['overdue'] > 0 ? 'Needs attention' : 'All clear'" />
        <x-ui.stat-card label="Open Tasks" :value="$stats['open_tasks']" icon="✓" tone="slate"
            :href="route('tasks.index')" />
    </div>

    <div class="mt-3 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <x-ui.stat-card label="Pending Quotations" :value="$stats['pending_quotations']" icon="📝" tone="brand"
            :href="route('quotations.index')" />
        <x-ui.stat-card label="Outstanding" :value="'RM ' . Number::format($stats['outstanding'])" icon="⏳" tone="amber"
            :href="route('invoices.outstanding')" />
        <x-ui.stat-card label="Payments (month)" :value="'RM ' . Number::format($stats['payments_this_month'])" icon="💰" tone="green"
            :href="route('payments.index')" />
        <x-ui.stat-card label="Overdue Invoices" :value="$stats['overdue_invoices']" icon="⚠️" tone="red"
            :href="route('invoices.outstanding')" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-3">
        {{-- Left: my tasks + deadlines --}}
        <div class="space-y-5 lg:col-span-2">
            <x-ui.card title="Today's Tasks" subtitle="Assigned to you, soonest first">
                <x-slot:actions>
                    <x-ui.button href="{{ route('tasks.index') }}" variant="ghost" size="sm">View all</x-ui.button>
                </x-slot:actions>

                @forelse ($myTasks as $task)
                    <a href="{{ route('projects.show', $task->project) }}" wire:navigate wire:key="task-{{ $task->id }}"
                       class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-2.5 hover:bg-slate-50">
                        <x-ui.badge :color="$task->status->color()" size="xs" dot>{{ $task->status->label() }}</x-ui.badge>
                        <span class="min-w-0 flex-1 truncate text-sm text-slate-700">{{ $task->title }}</span>
                        <span class="hidden shrink-0 text-xs text-slate-400 sm:inline">{{ $task->project->reference }}</span>
                        @if ($task->due_date)
                            <span @class([
                                'shrink-0 text-xs font-medium tabular-nums',
                                'text-red-600' => $task->is_overdue,
                                'text-slate-500' => ! $task->is_overdue,
                            ])>{{ $task->due_date->isToday() ? 'Today' : $task->due_date->format('d M') }}</span>
                        @endif
                    </a>
                @empty
                    <x-ui.empty-state icon="🎉" title="Inbox zero" description="You have no open tasks assigned right now." />
                @endforelse
            </x-ui.card>

            <x-ui.card title="Upcoming Deadlines">
                @forelse ($deadlines as $project)
                    <a href="{{ route('projects.show', $project) }}" wire:navigate wire:key="dl-{{ $project->id }}"
                       class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-2.5 hover:bg-slate-50">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-600">
                            {{ $project->target_end_date->format('d') }}<br>{{ $project->target_end_date->format('M') }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-800">{{ $project->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $project->customer->company_name }}</p>
                        </div>
                        <x-ui.badge :color="$project->health->color()" size="xs">{{ $project->health->label() }}</x-ui.badge>
                        <span class="hidden w-24 sm:block"><x-ui.progress :value="$project->progress" show-label size="sm" /></span>
                    </a>
                @empty
                    <x-ui.empty-state icon="🗓" title="No deadlines" description="No active projects have an upcoming target date." />
                @endforelse
            </x-ui.card>
        </div>

        {{-- Right: health + activity --}}
        <div class="space-y-5">
            <x-ui.card title="Project Health">
                @php
                    $total = max(1, array_sum($health));
                    $rows = [
                        ['label' => 'On Track', 'key' => ProjectHealth::OnTrack->value, 'bar' => 'bg-green-500', 'dot' => '🟢'],
                        ['label' => 'At Risk', 'key' => ProjectHealth::AtRisk->value, 'bar' => 'bg-amber-500', 'dot' => '🟡'],
                        ['label' => 'Overdue', 'key' => ProjectHealth::Overdue->value, 'bar' => 'bg-red-500', 'dot' => '🔴'],
                    ];
                @endphp
                <div class="space-y-3">
                    @foreach ($rows as $row)
                        <div wire:key="h-{{ $row['key'] }}">
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="text-slate-600">{{ $row['dot'] }} {{ $row['label'] }}</span>
                                <span class="font-semibold tabular-nums text-slate-900">{{ $health[$row['key']] }}</span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-1.5 rounded-full {{ $row['bar'] }}" style="width: {{ round($health[$row['key']] / $total * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card title="Recent Activity">
                <ol class="space-y-4">
                    @forelse ($activity as $entry)
                        <li class="flex gap-3" wire:key="act-{{ $entry->id }}">
                            <x-ui.avatar :name="$entry->causer?->name ?? 'System'" size="xs" class="mt-0.5" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-slate-700">
                                    <span class="font-medium text-slate-900">{{ $entry->causer?->name ?? 'System' }}</span>
                                    {{ $entry->description }}
                                    @if ($entry->subject_type)
                                        <span class="text-slate-500">{{ class_basename($entry->subject_type) }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-400">{{ $entry->created_at->diffForHumans() }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-400">No activity recorded yet.</li>
                    @endforelse
                </ol>
            </x-ui.card>
        </div>
    </div>
</div>
