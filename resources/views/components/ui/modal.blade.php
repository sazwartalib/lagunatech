@props([
    'name',
    'title' => null,
    'maxWidth' => 'lg',
])

@php
    $widths = [
        'sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-lg',
        'xl' => 'max-w-xl', '2xl' => 'max-w-2xl', '3xl' => 'max-w-3xl',
    ];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}' || $event.detail?.name === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}' || $event.detail?.name === '{{ $name }}' || !$event.detail) open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
>
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40" @click="open = false"></div>

    <div
        x-show="open"
        x-transition
        class="relative w-full {{ $widths[$maxWidth] ?? $widths['lg'] }} rounded-xl bg-white shadow-xl"
    >
        @if ($title)
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-slate-900">{{ $title }}</h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        @endif

        <div class="px-5 py-4">{{ $slot }}</div>

        @isset($footer)
            <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-3.5">{{ $footer }}</div>
        @endisset
    </div>
</div>
