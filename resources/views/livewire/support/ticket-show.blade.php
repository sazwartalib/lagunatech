@php $t = $ticket; @endphp

<div class="mx-auto max-w-3xl">
    <x-ui.page-header :title="$t->reference" :subtitle="$t->subject">
        <x-slot:breadcrumbs>
            <a href="{{ route('support.index') }}" wire:navigate class="hover:text-slate-600">Support</a>
            <span>/</span><span class="text-slate-500">{{ $t->reference }}</span>
        </x-slot:breadcrumbs>
    </x-ui.page-header>

    <div class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
        <x-ui.badge :color="$t->priority->color()" size="sm">{{ $t->priority->label() }}</x-ui.badge>
        <x-ui.badge :color="$t->status->color()" size="sm" dot>{{ $t->status->label() }}</x-ui.badge>
        <span class="text-sm text-slate-400">{{ $t->customer->company_name }}@if ($t->project) · {{ $t->project->name }}@endif</span>

        @can('update', $t)
            <div class="ml-auto flex flex-wrap items-center gap-2">
                <select wire:change="changeStatus($event.target.value)"
                        class="rounded-lg border-0 py-1.5 pl-2.5 pr-8 text-xs shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-brand-500">
                    @foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected($t->status->value === $value)>{{ $label }}</option>@endforeach
                </select>
                <select wire:model="assignTo" wire:change="assign"
                        class="rounded-lg border-0 py-1.5 pl-2.5 pr-8 text-xs shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-brand-500">
                    <option value="">Unassigned</option>
                    @foreach ($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}</option>@endforeach
                </select>
            </div>
        @endcan
    </div>

    {{-- Conversation --}}
    <x-ui.card>
        <div class="space-y-4">
            <div class="rounded-lg bg-slate-50 p-3">
                <p class="mb-1 text-xs text-slate-400">
                    Opened by {{ $t->openedBy?->name ?? 'staff' }} · {{ $t->created_at->format('d M Y, g:ia') }}
                </p>
                <p class="whitespace-pre-line text-sm text-slate-700">{{ $t->description }}</p>
            </div>

            @foreach ($t->replies as $rep)
                <div @class([
                    'rounded-lg p-3',
                    'bg-brand-50/60' => $rep->from_customer,
                    'bg-amber-50 ring-1 ring-inset ring-amber-200' => $rep->is_internal,
                    'bg-slate-50' => ! $rep->from_customer && ! $rep->is_internal,
                ]) wire:key="rep-{{ $rep->id }}">
                    <p class="mb-1 flex items-center gap-2 text-xs text-slate-400">
                        <span class="font-medium text-slate-600">{{ $rep->author_name }}</span>
                        {{ $rep->from_customer ? '(customer)' : '(staff)' }}
                        · {{ $rep->created_at->diffForHumans() }}
                        @if ($rep->is_internal)<x-ui.badge color="amber" size="xs">Internal</x-ui.badge>@endif
                    </p>
                    <p class="whitespace-pre-line text-sm text-slate-700">{{ $rep->body }}</p>
                </div>
            @endforeach
        </div>

        @can('reply', $t)
            <form wire:submit="postReply" class="mt-4 border-t border-slate-100 pt-4">
                <x-ui.field name="reply">
                    <x-ui.textarea wire:model="reply" rows="3" placeholder="Write a reply…" />
                </x-ui.field>
                <div class="mt-2 flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" wire:model="replyInternal" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                        Internal note (hidden from customer)
                    </label>
                    <x-ui.button type="submit" size="sm">Post reply</x-ui.button>
                </div>
            </form>
        @endcan
    </x-ui.card>
</div>
