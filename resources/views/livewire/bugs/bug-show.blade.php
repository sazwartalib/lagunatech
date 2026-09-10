@php $b = $bug; @endphp

<div class="mx-auto max-w-3xl">
    <x-ui.page-header :title="$b->reference" :subtitle="$b->title">
        <x-slot:breadcrumbs>
            <a href="{{ route('bugs.index') }}" wire:navigate class="hover:text-slate-600">Bugs</a>
            <span>/</span><span class="text-slate-500">{{ $b->reference }}</span>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            @can('update', $b)
                <x-ui.button variant="secondary" href="{{ route('bugs.edit', $b) }}" icon="✎">Edit</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-5 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3">
        <x-ui.badge :color="$b->priority->color()" size="sm">{{ $b->priority->label() }}</x-ui.badge>
        @can('update', $b)
            @foreach ($boardStatuses as $s)
                <button wire:click="setStatus('{{ $s->value }}')"
                    @class([
                        'rounded-full px-2.5 py-1 text-xs font-medium transition',
                        'bg-brand-600 text-white' => $b->status === $s,
                        'bg-slate-100 text-slate-600 hover:bg-slate-200' => $b->status !== $s,
                    ])>{{ $s->label() }}</button>
            @endforeach
        @else
            <x-ui.badge :color="$b->status->color()" size="sm" dot>{{ $b->status->label() }}</x-ui.badge>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="space-y-5 sm:col-span-2">
            <x-ui.card title="Description">
                <p class="whitespace-pre-line text-sm text-slate-700">{{ $b->description ?: 'No description provided.' }}</p>
            </x-ui.card>

            <x-ui.card title="Comments">
                @can('view', $b)
                    <form wire:submit="addComment" class="mb-3">
                        <x-ui.textarea wire:model="comment" rows="2" placeholder="Add a comment…" />
                        @error('comment')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <div class="mt-2 flex justify-end"><x-ui.button type="submit" size="sm">Comment</x-ui.button></div>
                    </form>
                @endcan
                <ol class="space-y-3">
                    @forelse ($b->comments as $c)
                        <li class="flex gap-3" wire:key="bc-{{ $c->id }}">
                            <x-ui.avatar :name="$c->user?->name ?? 'System'" size="xs" class="mt-0.5" />
                            <div>
                                <p class="text-xs text-slate-400"><span class="font-medium text-slate-600">{{ $c->user?->name ?? 'System' }}</span> · {{ $c->created_at->diffForHumans() }}</p>
                                <p class="whitespace-pre-line text-sm text-slate-700">{{ $c->body }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-400">No comments yet.</li>
                    @endforelse
                </ol>
            </x-ui.card>
        </div>

        <x-ui.card title="Details">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-400">Project</dt><dd><a href="{{ route('projects.show', $b->project) }}" wire:navigate class="font-medium text-brand-700 hover:underline">{{ $b->project->reference }}</a></dd></div>
                <div><dt class="text-slate-400">Reported by</dt><dd class="text-slate-800">{{ $b->reporter?->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Assigned to</dt><dd class="text-slate-800">{{ $b->assignee?->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Due</dt><dd class="{{ $b->is_overdue ? 'font-medium text-red-600' : 'text-slate-800' }}">{{ $b->due_date?->format('d M Y') ?? '—' }}</dd></div>
                @if ($b->resolved_at)
                    <div><dt class="text-slate-400">Resolved</dt><dd class="text-slate-800">{{ $b->resolved_at->format('d M Y') }}</dd></div>
                @endif
            </dl>
        </x-ui.card>
    </div>
</div>
