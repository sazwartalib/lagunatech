<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button type="button" @click="open = !open" class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100" aria-label="Notifications">
        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/></svg>
        @if ($unreadCount > 0)
            <span class="absolute right-1 top-1 grid min-h-4 min-w-4 place-items-center rounded-full bg-red-500 px-1 text-[10px] font-semibold text-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>

    <div x-show="open" x-cloak x-transition.origin.top.right
         class="absolute right-0 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-2.5">
            <span class="text-sm font-semibold text-slate-900">Notifications</span>
            @if ($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs font-medium text-brand-600 hover:text-brand-700">Mark all read</button>
            @endif
        </div>

        <div class="scrollbar-slim max-h-96 divide-y divide-slate-100 overflow-y-auto">
            @forelse ($items as $note)
                <a href="{{ $note->data['url'] ?? '#' }}" wire:navigate wire:key="n-{{ $note->id }}"
                   wire:click="markRead('{{ $note->id }}')"
                   @class(['flex gap-3 px-4 py-3 hover:bg-slate-50', 'bg-brand-50/40' => $note->read_at === null])>
                    <span class="text-lg">{{ $note->data['icon'] ?? '🔔' }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800">{{ $note->data['title'] ?? 'Notification' }}</p>
                        <p class="text-xs text-slate-500">{{ $note->data['body'] ?? '' }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ $note->created_at->diffForHumans() }}</p>
                    </div>
                    @if ($note->read_at === null)<span class="mt-1 size-2 shrink-0 rounded-full bg-brand-500"></span>@endif
                </a>
            @empty
                <p class="px-4 py-8 text-center text-sm text-slate-400">You're all caught up.</p>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}" wire:navigate @click="open = false"
           class="block border-t border-slate-100 px-4 py-2.5 text-center text-xs font-medium text-brand-600 hover:bg-slate-50">
            View all
        </a>
    </div>
</div>
