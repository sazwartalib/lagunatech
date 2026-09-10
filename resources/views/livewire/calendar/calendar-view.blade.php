@php
    $today = now()->toDateString();
    $typeLabels = ['project' => '🏁 Deadlines', 'task' => '✓ Tasks', 'milestone' => '◈ Milestones', 'meeting' => '🤝 Meetings', 'invoice' => '🧾 Invoices'];
@endphp

<div>
    <x-ui.page-header title="Calendar" subtitle="Deadlines, tasks, milestones, meetings and invoice dates." />

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <x-ui.button size="sm" variant="secondary" wire:click="shiftMonth(-1)">‹</x-ui.button>
            <span class="min-w-[9rem] text-center text-sm font-semibold text-slate-900">{{ $monthStart->format('F Y') }}</span>
            <x-ui.button size="sm" variant="secondary" wire:click="shiftMonth(1)">›</x-ui.button>
            <x-ui.button size="sm" variant="ghost" wire:click="today">Today</x-ui.button>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach ($typeLabels as $key => $label)
                <label class="flex items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1 text-xs">
                    <input type="checkbox" wire:model.live="types.{{ $key }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <x-ui.card flush>
        <div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50/70 text-center text-xs font-semibold text-slate-500">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dow)
                <div class="py-2">{{ $dow }}</div>
            @endforeach
        </div>
        <div class="grid grid-cols-7">
            @foreach ($days as $day)
                @php
                    $key = $day->toDateString();
                    $events = $eventsByDate[$key] ?? collect();
                    $isCurrentMonth = $day->month === $monthStart->month;
                @endphp
                <div wire:key="day-{{ $key }}"
                     @class([
                         'min-h-24 border-b border-r border-slate-100 p-1.5 last:border-r-0',
                         'bg-slate-50/40 text-slate-300' => ! $isCurrentMonth,
                     ])>
                    <div class="mb-1 text-right">
                        <span @class([
                            'inline-grid size-6 place-items-center rounded-full text-xs',
                            'bg-brand-600 font-semibold text-white' => $key === $today,
                            'text-slate-500' => $key !== $today && $isCurrentMonth,
                        ])>{{ $day->format('j') }}</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($events->take(4) as $event)
                            <a href="{{ $event['url'] }}" wire:navigate wire:key="ev-{{ $key }}-{{ $loop->index }}"
                               class="block truncate rounded px-1 py-0.5 text-[11px] leading-tight
                                   {{ [
                                       'brand' => 'bg-brand-50 text-brand-700',
                                       'slate' => 'bg-slate-100 text-slate-600',
                                       'purple' => 'bg-purple-50 text-purple-700',
                                       'blue' => 'bg-blue-50 text-blue-700',
                                       'amber' => 'bg-amber-50 text-amber-800',
                                   ][$event['color']] }}"
                               title="{{ $event['label'] }}">{{ $event['label'] }}</a>
                        @endforeach
                        @if ($events->count() > 4)
                            <p class="px-1 text-[10px] text-slate-400">+{{ $events->count() - 4 }} more</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.card>
</div>
