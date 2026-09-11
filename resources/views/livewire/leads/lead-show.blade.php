@php
    use App\Enums\LeadStatus;
    $l = $lead;
@endphp

<div class="mx-auto max-w-3xl">
    <x-ui.page-header :title="$l->name" :subtitle="$l->reference">
        <x-slot:breadcrumbs>
            <a href="{{ route('leads.index') }}" wire:navigate class="hover:text-slate-600">Leads</a>
            <span>/</span><span class="text-slate-500">{{ $l->reference }}</span>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            @can('convert', $l)
                <x-ui.button wire:click="convert" wire:confirm="Create a customer record from this lead?">Convert to Customer →</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    @if ($l->convertedCustomer)
        <div class="mb-5 rounded-lg bg-green-50 px-4 py-2.5 text-sm text-green-800 ring-1 ring-inset ring-green-600/20">
            Converted to customer
            <a href="{{ route('customers.show', $l->convertedCustomer) }}" wire:navigate class="font-semibold underline">{{ $l->convertedCustomer->reference }} · {{ $l->convertedCustomer->company_name }}</a>
        </div>
    @endif

    {{-- Workflow bar --}}
    <div class="mb-5 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3">
        <x-ui.badge :color="$l->status->color()" size="md" dot>{{ $l->status->label() }}</x-ui.badge>
        @can('update', $l)
            <select wire:change="setStatus($event.target.value)"
                    class="rounded-lg border-0 py-1.5 pl-2.5 pr-8 text-xs shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-brand-500">
                @foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected($l->status->value === $value)>{{ $label }}</option>@endforeach
            </select>
            <select wire:model="ownerId" wire:change="assign"
                    class="rounded-lg border-0 py-1.5 pl-2.5 pr-8 text-xs shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-brand-500">
                <option value="">Unassigned</option>
                @foreach ($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}</option>@endforeach
            </select>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="space-y-5 sm:col-span-2">
            <x-ui.card title="Enquiry">
                <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-400">Name</dt><dd class="text-slate-800">{{ $l->name }}</dd></div>
                    <div><dt class="text-slate-400">Company</dt><dd class="text-slate-800">{{ $l->company ?: '—' }}</dd></div>
                    <div><dt class="text-slate-400">Email</dt><dd class="text-slate-800">{{ $l->email }}</dd></div>
                    <div><dt class="text-slate-400">Phone</dt><dd class="text-slate-800">{{ $l->phone ?: '—' }}</dd></div>
                    <div><dt class="text-slate-400">Software category</dt><dd class="text-slate-800">{{ $l->project_type ?: '—' }}</dd></div>
                    <div><dt class="text-slate-400">Budget</dt><dd class="text-slate-800">{{ $l->budget_range ?: '—' }}</dd></div>
                    <div><dt class="text-slate-400">Color theme</dt><dd class="text-slate-800">{{ $l->color_theme ?: '—' }}</dd></div>
                    <div><dt class="text-slate-400">Slogan</dt><dd class="text-slate-800">{{ $l->slogan ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-400">Idea / flow</dt><dd class="whitespace-pre-line text-slate-700">{{ $l->message ?: '—' }}</dd></div>
                </dl>
            </x-ui.card>

            @if ($l->attachments->isNotEmpty())
                <x-ui.card title="Attachments">
                    <ul class="divide-y divide-slate-100">
                        @foreach ($l->attachments as $file)
                            <li class="flex items-center justify-between gap-3 py-2.5 text-sm" wire:key="la-{{ $file->id }}">
                                <div class="min-w-0">
                                    <p class="truncate font-medium text-slate-700">{{ $file->original_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $file->kind->label() }} · {{ $file->human_size }}</p>
                                </div>
                                <a href="{{ route('leads.attachments.download', $file) }}"
                                   class="shrink-0 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-200">Download</a>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif

            <x-ui.card title="Follow-up notes">
                @can('update', $l)
                    <form wire:submit="addNote" class="mb-3">
                        <textarea wire:model="note" rows="2" placeholder="Log a call, email or next step…"
                                  class="w-full rounded-lg border-0 px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600"></textarea>
                        @error('note')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        <div class="mt-2 flex justify-end"><x-ui.button type="submit" size="sm">Add note</x-ui.button></div>
                    </form>
                @endcan
                <ol class="space-y-3">
                    @forelse ($l->notes as $entry)
                        <li class="flex gap-3" wire:key="ln-{{ $entry->id }}">
                            <x-ui.avatar :name="$entry->user?->name ?? 'System'" size="xs" class="mt-0.5" />
                            <div>
                                <p class="text-xs text-slate-400"><span class="font-medium text-slate-600">{{ $entry->user?->name ?? 'System' }}</span> · {{ $entry->created_at->diffForHumans() }}</p>
                                <p class="whitespace-pre-line text-sm text-slate-700">{{ $entry->body }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-400">No notes yet.</li>
                    @endforelse
                </ol>
            </x-ui.card>
        </div>

        <x-ui.card title="Details">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-400">Source</dt><dd><x-ui.badge color="slate" size="xs">{{ $l->source->label() }}</x-ui.badge></dd></div>
                <div><dt class="text-slate-400">Owner</dt><dd class="text-slate-800">{{ $l->owner?->name ?? 'Unassigned' }}</dd></div>
                <div><dt class="text-slate-400">Received</dt><dd class="text-slate-800">{{ $l->created_at->format('d M Y, g:ia') }}</dd></div>
                @if ($l->contacted_at)<div><dt class="text-slate-400">First contacted</dt><dd class="text-slate-800">{{ $l->contacted_at->format('d M Y') }}</dd></div>@endif
                @if ($l->ip_address)<div><dt class="text-slate-400">IP</dt><dd class="text-slate-500">{{ $l->ip_address }}</dd></div>@endif
            </dl>
        </x-ui.card>
    </div>
</div>
