<div>
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $drawings->count() }} {{ Str::plural('drawing', $drawings->count()) }}</p>
        @can('create', \App\Models\Drawing::class)
            <x-ui.button size="sm" wire:click="$toggle('showCreate')" icon="＋">New drawing</x-ui.button>
        @endcan
    </div>

    @if ($showCreate)
        <x-ui.card class="mb-4">
            <form wire:submit="create" class="flex items-end gap-3">
                <div class="flex-1">
                    <x-ui.field label="Title" name="newTitle" required>
                        <x-ui.input wire:model="newTitle" placeholder="e.g. Homepage Wireframe" autofocus />
                    </x-ui.field>
                </div>
                <x-ui.button type="button" variant="secondary" wire:click="$set('showCreate', false)">Cancel</x-ui.button>
                <x-ui.button type="submit">Create</x-ui.button>
            </form>
        </x-ui.card>
    @endif

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($drawings as $drawing)
            <div wire:key="drawing-{{ $drawing->id }}" class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white">
                <a href="{{ route('drawings.show', $drawing) }}" class="block">
                    <div class="flex aspect-video items-center justify-center bg-slate-50">
                        @if ($drawing->thumbnail_path)
                            <img src="{{ asset('storage/'.$drawing->thumbnail_path) }}" alt="" class="h-full w-full object-contain">
                        @else
                            <span class="text-3xl text-slate-300">🎨</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="truncate text-sm font-medium text-slate-800">{{ $drawing->title }}</p>
                        <div class="mt-1 flex items-center gap-2">
                            <x-ui.badge :color="$drawing->status->color()" size="xs">{{ $drawing->status->label() }}</x-ui.badge>
                            <span class="text-xs text-slate-400">{{ $drawing->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
                @can('delete', $drawing)
                    <button wire:click="deleteDrawing({{ $drawing->id }})" wire:confirm="Delete this drawing?"
                            class="absolute right-2 top-2 hidden size-7 place-items-center rounded-full bg-white/90 text-slate-400 shadow hover:text-red-500 group-hover:grid">
                        ✕
                    </button>
                @endcan
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state icon="🎨" title="No drawings yet" description="Create a wireframe, flow diagram or concept to present to this client." />
            </div>
        @endforelse
    </div>
</div>
