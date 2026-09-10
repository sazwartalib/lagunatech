<div>
    <x-ui.page-header title="Meetings" subtitle="Scheduled and past customer meetings.">
        <x-slot:actions>
            @can('create', App\Models\Meeting::class)
                <x-ui.button href="{{ route('meetings.create') }}" icon="＋">Schedule meeting</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-4 inline-flex rounded-lg bg-slate-100 p-0.5 text-sm">
        @foreach (['upcoming' => 'Upcoming', 'past' => 'Past', 'all' => 'All'] as $key => $label)
            <button wire:click="$set('filter', '{{ $key }}')"
                @class(['rounded-md px-3 py-1 font-medium', 'bg-white text-slate-900 shadow-sm' => $filter === $key, 'text-slate-500' => $filter !== $key])>{{ $label }}</button>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse ($meetings as $meeting)
            <a href="{{ route('meetings.show', $meeting) }}" wire:navigate wire:key="mtg-{{ $meeting->id }}"
               class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 hover:border-slate-300">
                <div class="grid size-12 shrink-0 place-items-center rounded-lg bg-slate-100 text-center text-xs font-semibold text-slate-600">
                    {{ $meeting->scheduled_at->format('d') }}<br>{{ $meeting->scheduled_at->format('M') }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-slate-800">{{ $meeting->title }}</p>
                    <p class="truncate text-xs text-slate-500">
                        {{ $meeting->scheduled_at->format('g:ia') }} · {{ $meeting->duration_minutes }} min
                        @if ($meeting->location) · {{ $meeting->location }} @endif
                        @if ($meeting->project) · {{ $meeting->project->name }} @endif
                    </p>
                </div>
                @if ($meeting->action_items_count > 0)
                    <span class="text-xs text-slate-400">{{ $meeting->action_items_count }} {{ Str::plural('action', $meeting->action_items_count) }}</span>
                @endif
                <x-ui.badge :color="$meeting->status->color()" size="xs">{{ $meeting->status->label() }}</x-ui.badge>
            </a>
        @empty
            <x-ui.empty-state icon="🤝" title="No meetings"
                description="Schedule a meeting and capture its agenda, notes and action items in one place.">
                @can('create', App\Models\Meeting::class)
                    <x-ui.button href="{{ route('meetings.create') }}" icon="＋">Schedule meeting</x-ui.button>
                @endcan
            </x-ui.empty-state>
        @endforelse
    </div>

    @if ($meetings->hasPages())
        <div class="mt-4">{{ $meetings->links() }}</div>
    @endif
</div>
