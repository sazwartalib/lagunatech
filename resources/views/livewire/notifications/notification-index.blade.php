<div class="mx-auto max-w-2xl">
    <x-ui.page-header title="Notifications" :subtitle="$unread > 0 ? $unread . ' unread' : 'All caught up'">
        <x-slot:actions>
            @if ($unread > 0)
                <x-ui.button variant="secondary" wire:click="markAllRead">Mark all read</x-ui.button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card flush>
        <div class="divide-y divide-slate-100">
            @forelse ($items as $note)
                <a href="{{ $note->data['url'] ?? '#' }}" wire:navigate wire:key="ni-{{ $note->id }}"
                   wire:click="markRead('{{ $note->id }}')"
                   @class(['flex gap-3 px-4 py-3.5 hover:bg-slate-50', 'bg-brand-50/40' => $note->read_at === null])>
                    <span class="text-xl">{{ $note->data['icon'] ?? '🔔' }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800">{{ $note->data['title'] ?? 'Notification' }}</p>
                        <p class="text-sm text-slate-500">{{ $note->data['body'] ?? '' }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">{{ $note->created_at->diffForHumans() }}</p>
                    </div>
                    @if ($note->read_at === null)<span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-500"></span>@endif
                </a>
            @empty
                <div class="p-6"><x-ui.empty-state icon="🔔" title="No notifications" description="Task assignments, approvals and payments will show up here." /></div>
            @endforelse
        </div>
        @if ($items->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $items->links() }}</div>
        @endif
    </x-ui.card>
</div>
