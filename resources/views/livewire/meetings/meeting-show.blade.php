@php
    use App\Enums\MeetingStatus;
    $m = $meeting;
@endphp

<div class="mx-auto max-w-3xl">
    <x-ui.page-header :title="$m->title" :subtitle="$m->scheduled_at->format('l, d M Y · g:ia')">
        <x-slot:breadcrumbs>
            <a href="{{ route('meetings.index') }}" wire:navigate class="hover:text-slate-600">Meetings</a>
            <span>/</span><span class="text-slate-500">{{ $m->title }}</span>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            @can('update', $m)
                <x-ui.button variant="secondary" href="{{ route('meetings.edit', $m) }}" icon="✎">Edit</x-ui.button>
                @if ($m->status === MeetingStatus::Scheduled)
                    <x-ui.button wire:click="complete">Mark complete</x-ui.button>
                @endif
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-5 flex flex-wrap items-center gap-3 text-sm">
        <x-ui.badge :color="$m->status->color()" size="md" dot>{{ $m->status->label() }}</x-ui.badge>
        <span class="text-slate-500">{{ $m->duration_minutes }} min</span>
        @if ($m->location)<span class="text-slate-500">· {{ $m->location }}</span>@endif
        @if ($m->project)<a href="{{ route('projects.show', $m->project) }}" wire:navigate class="text-brand-600 hover:underline">· {{ $m->project->reference }}</a>@endif
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="space-y-5 sm:col-span-2">
            @if ($m->agenda)
                <x-ui.card title="Agenda"><p class="whitespace-pre-line text-sm text-slate-700">{{ $m->agenda }}</p></x-ui.card>
            @endif

            <x-ui.card title="Action items">
                @forelse ($m->actionItems as $item)
                    <div class="flex items-center gap-3 border-b border-slate-100 py-2.5 last:border-0" wire:key="mai-{{ $item->id }}">
                        @can('update', $m)
                            <button wire:click="toggleActionItem({{ $item->id }})"
                                class="grid size-5 shrink-0 place-items-center rounded border-2 {{ $item->is_done ? 'border-green-500 bg-green-500 text-white' : 'border-slate-300 text-transparent' }}">
                                <svg class="size-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @endcan
                        <div class="min-w-0 flex-1">
                            <p class="text-sm {{ $item->is_done ? 'text-slate-400 line-through' : 'text-slate-700' }}">{{ $item->description }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $item->owner?->name ?? 'Unassigned' }}
                                @if ($item->due_date) · due {{ $item->due_date->format('d M') }} @endif
                            </p>
                        </div>
                        @if ($item->task)
                            <x-ui.badge color="green" size="xs">Task ✓</x-ui.badge>
                        @elseif ($m->project_id)
                            @can('update', $m)
                                <button wire:click="convertActionItem({{ $item->id }})" class="text-xs font-medium text-brand-600 hover:text-brand-700">→ Task</button>
                            @endcan
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No action items.</p>
                @endforelse
            </x-ui.card>

            <x-ui.card title="Notes">
                @can('update', $m)
                    <x-ui.textarea wire:model="notes" rows="4" placeholder="Minutes and decisions…" />
                    <div class="mt-2 flex justify-end"><x-ui.button size="sm" wire:click="saveNotes">Save notes</x-ui.button></div>
                @else
                    <p class="whitespace-pre-line text-sm text-slate-700">{{ $m->notes ?: 'No notes recorded.' }}</p>
                @endcan
            </x-ui.card>
        </div>

        <x-ui.card title="Participants">
            <ul class="space-y-2">
                @forelse ($m->participants as $p)
                    <li class="flex items-center gap-2 text-sm" wire:key="mp-{{ $p->id }}">
                        <x-ui.avatar :name="$p->display_name" size="xs" />
                        <span class="text-slate-700">{{ $p->display_name }}</span>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">No participants listed.</li>
                @endforelse
            </ul>
            <p class="mt-4 text-xs text-slate-400">Organised by {{ $m->organizer?->name ?? '—' }}</p>
        </x-ui.card>
    </div>
</div>
