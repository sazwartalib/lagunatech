@php $p = $project; @endphp

<div>
    <div class="mb-1 text-xs text-slate-400">
        <a href="{{ route('portal.projects') }}" wire:navigate class="hover:text-slate-600">Projects</a> / {{ $p->name }}
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold tracking-tight text-slate-900">{{ $p->name }}</h1>
        <x-ui.badge :color="$p->status->color()" size="md" dot>{{ $p->status->label() }}</x-ui.badge>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="space-y-5 sm:col-span-2">
            <x-ui.card title="Progress">
                <x-ui.progress :value="$p->progress" show-label size="lg" />
                <div class="mt-4 space-y-3">
                    @foreach ($p->milestones as $milestone)
                        <div wire:key="pm-{{ $milestone->id }}">
                            <div class="mb-1 flex justify-between text-sm">
                                <span class="text-slate-700">{{ $milestone->name }}</span>
                                <span class="font-medium tabular-nums text-slate-500">{{ $milestone->progress }}%</span>
                            </div>
                            <x-ui.progress :value="$milestone->progress" :color="$milestone->progress === 100 ? 'green' : 'brand'" size="md" />
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            @if ($p->description)
                <x-ui.card title="About this project">
                    <p class="whitespace-pre-line text-sm text-slate-700">{{ $p->description }}</p>
                </x-ui.card>
            @endif

            <x-ui.card title="Shared documents">
                @forelse ($p->documents as $doc)
                    <div class="flex items-center gap-3 border-b border-slate-100 py-2.5 last:border-0" wire:key="pd-{{ $doc->id }}">
                        <span class="text-lg">📄</span>
                        <a href="{{ route('portal.documents.download', $doc) }}" class="flex-1 truncate text-sm font-medium text-brand-700 hover:underline">{{ $doc->title }}</a>
                        <span class="text-xs text-slate-400">{{ $doc->human_size }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No documents have been shared yet.</p>
                @endforelse
            </x-ui.card>

            <x-ui.card title="Change requests">
                <x-slot:actions>
                    <x-ui.button size="sm" variant="secondary" wire:click="$toggle('showChangeForm')" icon="＋">Request a change</x-ui.button>
                </x-slot:actions>

                @if ($showChangeForm)
                    <form wire:submit="submitChangeRequest" class="mb-4 space-y-3 rounded-lg bg-slate-50 p-3">
                        <x-ui.field label="What would you like changed?" name="crTitle" required>
                            <x-ui.input wire:model="crTitle" placeholder="e.g. Add an export to Excel button" />
                        </x-ui.field>
                        <x-ui.field label="Details" name="crDescription" required>
                            <x-ui.textarea wire:model="crDescription" rows="3" />
                        </x-ui.field>
                        <div class="flex justify-end gap-2">
                            <x-ui.button type="button" size="sm" variant="secondary" wire:click="$set('showChangeForm', false)">Cancel</x-ui.button>
                            <x-ui.button type="submit" size="sm">Submit</x-ui.button>
                        </div>
                    </form>
                @endif

                @forelse ($changeRequests as $cr)
                    <div class="flex items-center gap-3 border-b border-slate-100 py-2.5 last:border-0" wire:key="pcr-{{ $cr->id }}">
                        <span class="min-w-0 flex-1 truncate text-sm text-slate-700">{{ $cr->title }}</span>
                        <x-ui.badge :color="$cr->status->color()" size="xs">{{ $cr->status->label() }}</x-ui.badge>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No change requests for this project.</p>
                @endforelse
            </x-ui.card>
        </div>

        <x-ui.card title="Details">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-400">Project manager</dt><dd class="text-slate-800">{{ $p->lead?->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Type</dt><dd class="text-slate-800">{{ $p->type ?: '—' }}</dd></div>
                <div><dt class="text-slate-400">Started</dt><dd class="text-slate-800">{{ $p->start_date?->format('d M Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Target completion</dt><dd class="text-slate-800">{{ $p->target_end_date?->format('d M Y') ?? 'TBC' }}</dd></div>
            </dl>
        </x-ui.card>
    </div>
</div>
