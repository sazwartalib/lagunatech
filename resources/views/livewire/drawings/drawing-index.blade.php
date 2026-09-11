<div>
    <x-ui.page-header title="Drawings" subtitle="Wireframes, diagrams and concepts across every project." />

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
                <x-ui.empty-state icon="🎨" title="No drawings yet" description="Open a project and create one from its Drawings tab." />
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $drawings->links() }}</div>
</div>
