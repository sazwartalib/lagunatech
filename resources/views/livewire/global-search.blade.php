<div
    x-data="{ open: false }"
    @keydown.window.prevent.cmd.k="open = true"
    @keydown.window.prevent.ctrl.k="open = true"
    @keydown.escape="open = false"
    class="flex-1 sm:flex-none"
>
    <button type="button" @click="open = true"
        class="flex w-full items-center gap-2 rounded-lg bg-slate-100 px-2.5 py-1.5 text-sm text-slate-400 hover:bg-slate-200/70 sm:w-64">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
        <span class="flex-1 text-left">Search…</span>
        <kbd class="hidden rounded border border-slate-300 bg-white px-1 text-[10px] font-medium text-slate-400 sm:inline">⌘K</kbd>
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-[10vh]">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40" @click="open = false"></div>

        <div x-show="open" x-transition
             class="relative w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-2xl"
             @keydown.escape.stop="open = false">
            <div class="flex items-center gap-2 border-b border-slate-100 px-4">
                <svg class="size-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.250ms="q"
                    placeholder="Search customers, projects, tasks…"
                    class="w-full border-0 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0"
                    x-ref="input"
                    x-effect="open && $nextTick(() => $refs.input.focus())"
                >
                <div wire:loading class="text-xs text-slate-400">…</div>
            </div>

            <div class="scrollbar-slim max-h-80 overflow-y-auto p-2">
                @php $grouped = collect($results)->groupBy('group'); @endphp

                @if (strlen(trim($q)) < 2)
                    <p class="px-3 py-6 text-center text-sm text-slate-400">Type at least 2 characters to search.</p>
                @elseif ($grouped->isEmpty())
                    <p class="px-3 py-6 text-center text-sm text-slate-400">No matches for “{{ $q }}”.</p>
                @else
                    @foreach ($grouped as $group => $items)
                        <p class="px-3 pb-1 pt-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $group }}</p>
                        @foreach ($items as $item)
                            <a href="{{ $item['url'] }}" wire:navigate @click="open = false"
                               wire:key="res-{{ $loop->parent->index }}-{{ $loop->index }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm hover:bg-slate-100">
                                <span>{{ $item['icon'] }}</span>
                                <span class="flex-1 truncate text-slate-700">{{ $item['label'] }}</span>
                                <span class="truncate text-xs text-slate-400">{{ $item['meta'] }}</span>
                            </a>
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
