<div>
    <x-ui.page-header title="Drawings" subtitle="Wireframes, diagrams and concepts — standalone or linked to a project.">
        <x-slot:actions>
            @can('create', \App\Models\Drawing::class)
                <x-ui.button size="sm" wire:click="$toggle('showCreate')" icon="＋">New drawing</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    @if ($showCreate)
        <x-ui.card class="mb-4">
            <form wire:submit="create" class="flex items-end gap-3">
                <div class="flex-1">
                    <x-ui.field label="Title" name="newTitle" required>
                        <x-ui.input wire:model="newTitle" placeholder="e.g. Client Workshop Sketch" autofocus />
                    </x-ui.field>
                </div>
                <x-ui.button type="button" variant="secondary" wire:click="$set('showCreate', false)">Cancel</x-ui.button>
                <x-ui.button type="submit">Create</x-ui.button>
            </form>
        </x-ui.card>
    @endif

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <div class="w-full max-w-xs">
            <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search by title…" />
        </div>
        <x-ui.select wire:model.live="status" :options="['' => 'All statuses'] + $statuses" class="w-40" />
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($drawings as $drawing)
            <a href="{{ route('drawings.show', $drawing) }}" wire:key="drawing-{{ $drawing->id }}"
               class="overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:shadow-sm">
                <div class="flex aspect-video items-center justify-center bg-slate-50">
                    @if ($drawing->thumbnail_path)
                        <img src="{{ asset('storage/'.$drawing->thumbnail_path) }}" alt="" class="h-full w-full object-contain">
                    @else
                        <span class="text-3xl text-slate-300">🎨</span>
                    @endif
                </div>
                <div class="p-3">
                    <p class="truncate text-sm font-medium text-slate-800">{{ $drawing->title }}</p>
                    <p class="truncate text-xs text-slate-400">{{ $drawing->drawable?->name ?? $drawing->drawable?->title ?? '—' }}</p>
                    <div class="mt-1.5 flex items-center gap-2">
                        <x-ui.badge :color="$drawing->status->color()" size="xs">{{ $drawing->status->label() }}</x-ui.badge>
                        <span class="text-xs text-slate-400">{{ $drawing->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state icon="🎨" title="No drawings yet" description="Create one above, or open a project's Drawings tab to start from there." />
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $drawings->links() }}</div>
</div>
