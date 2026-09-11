@props([
    'drawing',
    'canEdit' => false,
    'canExport' => false,
    'chrome' => true,
    'fullscreen' => false,
])

@php
    $toolBtn = 'inline-flex size-8 items-center justify-center rounded-lg text-sm text-slate-600 transition hover:bg-slate-200 data-[active=true]:bg-brand-100 data-[active=true]:text-brand-700';
@endphp

<div
    data-drawing-editor
    data-drawing-id="{{ $drawing->id }}"
    data-can-edit="{{ $canEdit ? '1' : '0' }}"
    data-can-export="{{ $canExport ? '1' : '0' }}"
    data-save-url="{{ route('drawings.update', $drawing) }}"
    data-image-url="{{ route('drawings.images.store', $drawing) }}"
    data-export-pdf-url="{{ route('drawings.export.pdf', $drawing) }}"
    data-drawing-title="{{ $drawing->title }}"
    wire:ignore
    @class([
        'flex flex-col overflow-hidden bg-white',
        'h-full' => $fullscreen,
        'h-[calc(100vh-9rem)] min-h-[420px] rounded-xl border border-slate-200' => ! $fullscreen,
    ])
>
    {{-- Toolbar --}}
    <div @class(['flex flex-wrap items-center gap-1 overflow-x-auto border-b border-slate-200 bg-slate-50 px-2 py-1.5' => true, 'hidden' => ! $chrome]) data-drawing-toolbar>
        @if ($canEdit)
            <button type="button" data-tool="select" data-active="true" title="Select (V)" class="{{ $toolBtn }}">↖</button>
            <button type="button" data-tool="hand" title="Pan (Space)" class="{{ $toolBtn }}">✋</button>
            <span class="mx-1 h-5 w-px shrink-0 bg-slate-200"></span>
            <button type="button" data-tool="pen" title="Pen" class="{{ $toolBtn }}">✎</button>
            <button type="button" data-tool="highlighter" title="Highlighter" class="{{ $toolBtn }}">🖊</button>
            <button type="button" data-tool="eraser" title="Eraser — click a shape to remove it" class="{{ $toolBtn }}">⌫</button>
            <span class="mx-1 h-5 w-px shrink-0 bg-slate-200"></span>
            <button type="button" data-tool="line" title="Line" class="{{ $toolBtn }}">╱</button>
            <button type="button" data-tool="arrow" title="Arrow" class="{{ $toolBtn }}">↗</button>
            <button type="button" data-tool="rectangle" title="Rectangle" class="{{ $toolBtn }}">▭</button>
            <button type="button" data-tool="ellipse" title="Ellipse" class="{{ $toolBtn }}">◯</button>
            <button type="button" data-tool="text" title="Text" class="{{ $toolBtn }}">T</button>
            <button type="button" data-tool="sticky" title="Sticky note" class="{{ $toolBtn }}">🗒</button>
            <span class="mx-1 h-5 w-px shrink-0 bg-slate-200"></span>
            <button type="button" data-action="image" title="Insert image" class="{{ $toolBtn }}">🖼</button>
            <input type="file" accept="image/png,image/jpeg,image/webp" data-image-input class="hidden">
            <span class="mx-1 h-5 w-px shrink-0 bg-slate-200"></span>
            <button type="button" data-action="undo" title="Undo (Ctrl+Z)" class="{{ $toolBtn }}">↺</button>
            <button type="button" data-action="redo" title="Redo (Ctrl+Shift+Z)" class="{{ $toolBtn }}">↻</button>
            <button type="button" data-action="duplicate" title="Duplicate (Ctrl+D)" class="{{ $toolBtn }}">⧉</button>
            <button type="button" data-action="delete" title="Delete (Del)" class="{{ $toolBtn }}">🗑</button>
        @endif

        <div class="ml-auto flex shrink-0 items-center gap-2 pl-2">
            <span data-save-status class="text-xs text-slate-400"></span>
            @if ($canEdit)
                <button type="button" data-action="save" class="rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-700">Save</button>
            @endif
            @if ($canExport)
                <button type="button" data-action="export-png" class="rounded-lg bg-white px-3 py-1.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">PNG</button>
                <button type="button" data-action="export-pdf" class="rounded-lg bg-white px-3 py-1.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">PDF</button>
            @endif
        </div>
    </div>

    {{-- Canvas --}}
    <div class="relative flex-1 overflow-hidden bg-slate-100" data-canvas-container>
        <canvas data-canvas></canvas>
    </div>
    <script type="application/json" data-canvas-json>{!! json_encode($drawing->canvas_data ?: ['version' => '6.0.0', 'objects' => []]) !!}</script>

    @once('drawing-editor-assets')
        @vite('resources/js/drawing.js')
    @endonce

    {{-- Zoom bar --}}
    <div class="flex items-center justify-center gap-2 border-t border-slate-200 bg-slate-50 px-3 py-1.5 text-xs text-slate-500">
        <button type="button" data-action="zoom-out" title="Zoom out" class="{{ $toolBtn }}">－</button>
        <span data-zoom-level class="w-10 text-center tabular-nums">100%</span>
        <button type="button" data-action="zoom-in" title="Zoom in" class="{{ $toolBtn }}">＋</button>
        <button type="button" data-action="zoom-fit" class="ml-2 rounded-lg px-2 py-1 hover:bg-slate-200">Fit</button>
    </div>
</div>
