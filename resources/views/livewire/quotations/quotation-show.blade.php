@php
    use Illuminate\Support\Number;
    use App\Enums\QuotationStatus;
    $q = $quotation;
@endphp

<div class="mx-auto max-w-4xl">
    <x-ui.page-header :title="$q->reference" :subtitle="$q->title">
        <x-slot:breadcrumbs>
            <a href="{{ route('quotations.index') }}" wire:navigate class="hover:text-slate-600">Quotations</a>
            <span>/</span><span class="text-slate-500">{{ $q->reference }}</span>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            <x-ui.button variant="secondary" href="{{ route('quotations.pdf', $q) }}" target="_blank" icon="⬇">PDF</x-ui.button>
            @can('update', $q)
                <x-ui.button variant="secondary" href="{{ route('quotations.edit', $q) }}" icon="✎">Edit</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Status + workflow bar --}}
    <div class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
        <x-ui.badge :color="$q->status->color()" size="md" dot>{{ $q->status->label() }}</x-ui.badge>
        <span class="text-sm text-slate-400">Total <span class="font-semibold text-slate-800">RM {{ Number::format((float) $q->total, 2) }}</span></span>
        <div class="ml-auto flex flex-wrap gap-2">
            @can('update', $q)
                @if ($q->status === QuotationStatus::Draft)
                    <x-ui.button size="sm" wire:click="changeStatus('sent')">Mark as sent</x-ui.button>
                @endif
            @endcan
            @can('decide', $q)
                @if (in_array($q->status, [QuotationStatus::Sent, QuotationStatus::Viewed], true))
                    <x-ui.button size="sm" wire:click="changeStatus('approved')"
                        wire:confirm="Approve this quotation?">Approve</x-ui.button>
                    <x-ui.button size="sm" variant="danger-ghost" wire:click="changeStatus('rejected')"
                        wire:confirm="Reject this quotation?">Reject</x-ui.button>
                @endif
            @endcan
            @can('convert', $q)
                @if ($q->status === QuotationStatus::Approved && ! $q->project_id)
                    <x-ui.button size="sm" wire:click="convertToProject" wire:confirm="Create a project from this quotation?">
                        Convert to Project →
                    </x-ui.button>
                @endif
            @endcan
            @can('create', App\Models\Invoice::class)
                @if ($q->status === QuotationStatus::Approved)
                    <x-ui.button size="sm" variant="secondary" wire:click="createInvoice">Create invoice</x-ui.button>
                @endif
            @endcan
        </div>
    </div>

    @if ($q->project)
        <div class="mb-5 rounded-lg bg-green-50 px-4 py-2.5 text-sm text-green-800 ring-1 ring-inset ring-green-600/20">
            Linked to project
            <a href="{{ route('projects.show', $q->project) }}" wire:navigate class="font-semibold underline">{{ $q->project->reference }} · {{ $q->project->name }}</a>
        </div>
    @endif

    {{-- Document body --}}
    <x-ui.card>
        <div class="flex flex-wrap justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-400">Bill to</p>
                <p class="mt-1 font-medium text-slate-900">{{ $q->customer->company_name }}</p>
                <p class="text-sm text-slate-500">{{ $q->customer->contact_person }}</p>
                <p class="text-sm text-slate-500">{{ $q->customer->email }}</p>
            </div>
            <dl class="text-sm">
                <div class="flex justify-between gap-8"><dt class="text-slate-400">Issued</dt><dd class="text-slate-700">{{ $q->issue_date->format('d M Y') }}</dd></div>
                <div class="flex justify-between gap-8"><dt class="text-slate-400">Valid until</dt><dd class="text-slate-700">{{ $q->valid_until?->format('d M Y') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-8"><dt class="text-slate-400">Prepared by</dt><dd class="text-slate-700">{{ $q->creator?->name ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase text-slate-400">
                        <th class="py-2 text-left font-semibold">Description</th>
                        <th class="py-2 text-right font-semibold">Qty</th>
                        <th class="py-2 text-right font-semibold">Unit price</th>
                        <th class="py-2 text-right font-semibold">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($q->items as $item)
                        <tr wire:key="qi-{{ $item->id }}">
                            <td class="py-2.5 text-slate-700">{{ $item->description }}</td>
                            <td class="py-2.5 text-right tabular-nums text-slate-600">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                            <td class="py-2.5 text-right tabular-nums text-slate-600">{{ Number::format((float) $item->unit_price, 2) }}</td>
                            <td class="py-2.5 text-right tabular-nums font-medium text-slate-800">{{ Number::format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-end">
            <dl class="w-full max-w-xs space-y-1 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="tabular-nums">RM {{ Number::format((float) $q->subtotal, 2) }}</dd></div>
                @if ((float) $q->discount_amount > 0)
                    <div class="flex justify-between"><dt class="text-slate-500">Discount</dt><dd class="tabular-nums">− RM {{ Number::format((float) $q->discount_amount, 2) }}</dd></div>
                @endif
                @if ((float) $q->tax_amount > 0)
                    <div class="flex justify-between"><dt class="text-slate-500">Tax ({{ rtrim(rtrim(number_format((float) $q->tax_rate, 2), '0'), '.') }}%)</dt><dd class="tabular-nums">RM {{ Number::format((float) $q->tax_amount, 2) }}</dd></div>
                @endif
                <div class="flex justify-between border-t border-slate-200 pt-1 text-base font-semibold"><dt>Total</dt><dd class="tabular-nums">RM {{ Number::format((float) $q->total, 2) }}</dd></div>
            </dl>
        </div>

        @if ($q->terms)
            <div class="mt-6 border-t border-slate-100 pt-4">
                <p class="text-xs font-semibold uppercase text-slate-400">Terms &amp; conditions</p>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $q->terms }}</p>
            </div>
        @endif
    </x-ui.card>
</div>
