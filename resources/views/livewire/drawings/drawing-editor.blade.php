<div>
    <div class="mb-3 flex items-center gap-3">
        <a href="{{ url()->previous(route('drawings.index')) }}" wire:navigate class="text-sm text-slate-400 hover:text-slate-600">← Back</a>

        @if ($canEdit)
            <input type="text" wire:model.blur="title" wire:key="title-{{ $drawing->id }}"
                   class="min-w-0 flex-1 rounded-lg border-0 bg-transparent px-1 text-lg font-semibold text-slate-900 focus:bg-slate-50 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        @else
            <h1 class="min-w-0 flex-1 truncate px-1 text-lg font-semibold text-slate-900">{{ $drawing->title }}</h1>
        @endif

        <a href="{{ route('drawings.present', $drawing) }}" target="_blank"
           class="rounded-lg bg-white px-3 py-1.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
            Present ↗
        </a>
    </div>

    <x-drawing.editor :drawing="$drawing" :can-edit="$canEdit" :can-export="$canExport" wire:key="editor-{{ $drawing->id }}" />
</div>
