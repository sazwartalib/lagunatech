<div>
    <x-ui.page-header title="Support Tickets" subtitle="Requests raised by customers.">
        <x-slot:actions>
            @can('create', App\Models\SupportTicket::class)
                <x-ui.button href="{{ route('support.create') }}" icon="＋">New Ticket</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    @if ($openCount > 0)
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-2.5 text-sm text-red-800 ring-1 ring-inset ring-red-600/20">
            {{ $openCount }} open {{ Str::plural('ticket', $openCount) }}.
        </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <div class="relative min-w-[12rem] flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search reference or subject…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
        <select wire:model.live="status" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">Any status</option>
            @foreach ($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" wire:model.live="mineOnly" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600"> Assigned to me
        </label>
    </div>

    <x-ui.card flush>
        <div class="divide-y divide-slate-100">
            @forelse ($tickets as $ticket)
                <a href="{{ route('support.show', $ticket) }}" wire:navigate wire:key="tk-{{ $ticket->id }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50">
                    <span class="text-xs tabular-nums text-slate-400">{{ $ticket->reference }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-800">{{ $ticket->subject }}</p>
                        <p class="truncate text-xs text-slate-400">{{ $ticket->customer->company_name }} · {{ $ticket->created_at->diffForHumans() }}</p>
                    </div>
                    <x-ui.badge :color="$ticket->priority->color()" size="xs">{{ $ticket->priority->label() }}</x-ui.badge>
                    <x-ui.badge :color="$ticket->status->color()" size="xs" dot>{{ $ticket->status->label() }}</x-ui.badge>
                    @if ($ticket->assignee)<x-ui.avatar :name="$ticket->assignee->name" size="xs" />@else<span class="size-6"></span>@endif
                </a>
            @empty
                <div class="p-6"><x-ui.empty-state icon="🎫" title="No tickets" description="Support requests will appear here." /></div>
            @endforelse
        </div>
        @if ($tickets->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $tickets->links() }}</div>
        @endif
    </x-ui.card>
</div>
