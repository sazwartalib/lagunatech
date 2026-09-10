@php
    use Illuminate\Support\Number;
    use App\Enums\InvoiceStatus;
    $inv = $invoice;
@endphp

<div class="mx-auto max-w-4xl">
    <x-ui.page-header :title="$inv->reference" :subtitle="$inv->title">
        <x-slot:breadcrumbs>
            <a href="{{ route('invoices.index') }}" wire:navigate class="hover:text-slate-600">Invoices</a>
            <span>/</span><span class="text-slate-500">{{ $inv->reference }}</span>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            <x-ui.button variant="secondary" href="{{ route('invoices.pdf', $inv) }}" target="_blank" icon="⬇">PDF</x-ui.button>
            @can('update', $inv)
                <x-ui.button variant="secondary" href="{{ route('invoices.edit', $inv) }}" icon="✎">Edit</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <x-ui.stat-card label="Total" :value="'RM ' . Number::format((float) $inv->total, 2)" />
        <x-ui.stat-card label="Paid" :value="'RM ' . Number::format((float) $inv->amount_paid, 2)" tone="green" />
        <x-ui.stat-card label="Outstanding" :value="'RM ' . Number::format($inv->outstanding, 2)"
            :tone="$inv->outstanding > 0 ? 'amber' : 'green'" />
        <x-ui.stat-card label="Due" :value="$inv->due_date->format('d M Y')" :tone="$inv->is_overdue ? 'red' : 'slate'" />
    </div>

    {{-- Workflow bar --}}
    <div class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
        <x-ui.badge :color="$inv->status->color()" size="md" dot>{{ $inv->status->label() }}</x-ui.badge>
        @if ($inv->quotation)
            <a href="{{ route('quotations.show', $inv->quotation) }}" wire:navigate class="text-sm text-brand-600 hover:underline">from {{ $inv->quotation->reference }}</a>
        @endif
        <div class="ml-auto flex flex-wrap gap-2">
            @can('update', $inv)
                @if ($inv->status === InvoiceStatus::Draft)
                    <x-ui.button size="sm" wire:click="markSent">Mark as sent</x-ui.button>
                @endif
                @if ($inv->status->isOpen())
                    <x-ui.button size="sm" variant="danger-ghost" wire:click="cancel" wire:confirm="Cancel this invoice?">Cancel</x-ui.button>
                @endif
            @endcan
            @can('recordPayment', $inv)
                <x-ui.button size="sm" wire:click="$set('showPaymentModal', true)" icon="＋">Record payment</x-ui.button>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-ui.card>
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400">Bill to</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $inv->customer->company_name }}</p>
                        <p class="text-sm text-slate-500">{{ $inv->customer->contact_person }}</p>
                        <p class="text-sm text-slate-500">{{ $inv->customer->email }}</p>
                    </div>
                    <dl class="text-sm">
                        <div class="flex justify-between gap-8"><dt class="text-slate-400">Issued</dt><dd class="text-slate-700">{{ $inv->issue_date->format('d M Y') }}</dd></div>
                        <div class="flex justify-between gap-8"><dt class="text-slate-400">Due</dt><dd class="text-slate-700">{{ $inv->due_date->format('d M Y') }}</dd></div>
                        @if ($inv->project)
                            <div class="flex justify-between gap-8"><dt class="text-slate-400">Project</dt><dd class="text-slate-700">{{ $inv->project->reference }}</dd></div>
                        @endif
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
                            @foreach ($inv->items as $item)
                                <tr wire:key="ii-{{ $item->id }}">
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
                        <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="tabular-nums">RM {{ Number::format((float) $inv->subtotal, 2) }}</dd></div>
                        @if ((float) $inv->discount_amount > 0)
                            <div class="flex justify-between"><dt class="text-slate-500">Discount</dt><dd class="tabular-nums">− RM {{ Number::format((float) $inv->discount_amount, 2) }}</dd></div>
                        @endif
                        @if ((float) $inv->tax_amount > 0)
                            <div class="flex justify-between"><dt class="text-slate-500">Tax</dt><dd class="tabular-nums">RM {{ Number::format((float) $inv->tax_amount, 2) }}</dd></div>
                        @endif
                        <div class="flex justify-between border-t border-slate-200 pt-1 font-semibold"><dt>Total</dt><dd class="tabular-nums">RM {{ Number::format((float) $inv->total, 2) }}</dd></div>
                        <div class="flex justify-between text-green-700"><dt>Paid</dt><dd class="tabular-nums">− RM {{ Number::format((float) $inv->amount_paid, 2) }}</dd></div>
                        <div class="flex justify-between border-t border-slate-200 pt-1 text-base font-semibold"><dt>Balance due</dt><dd class="tabular-nums">RM {{ Number::format($inv->outstanding, 2) }}</dd></div>
                    </dl>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card title="Payments">
            @forelse ($inv->payments as $payment)
                <div class="border-b border-slate-100 py-2.5 last:border-0" wire:key="pay-{{ $payment->id }}">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-800">RM {{ Number::format((float) $payment->amount, 2) }}</span>
                        <x-ui.badge :color="$payment->method->color()" size="xs">{{ $payment->method->label() }}</x-ui.badge>
                    </div>
                    <p class="text-xs text-slate-400">
                        {{ $payment->paid_on->format('d M Y') }}
                        @if ($payment->reference_number) · {{ $payment->reference_number }} @endif
                        · {{ $payment->recorder?->name }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-slate-400">No payments recorded.</p>
            @endforelse
        </x-ui.card>
    </div>

    {{-- Record payment modal --}}
    <div x-data x-show="$wire.showPaymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/40" wire:click="$set('showPaymentModal', false)"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3.5">
                <h3 class="text-sm font-semibold text-slate-900">Record payment · {{ $inv->reference }}</h3>
                <button type="button" wire:click="$set('showPaymentModal', false)" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form wire:submit="recordPayment" class="space-y-3 px-5 py-4">
                <x-ui.field label="Amount (RM)" name="payAmount" required>
                    <x-ui.input type="number" step="0.01" wire:model="payAmount" />
                </x-ui.field>
                <div class="grid grid-cols-2 gap-3">
                    <x-ui.field label="Paid on" name="payDate" required>
                        <x-ui.input type="date" wire:model="payDate" />
                    </x-ui.field>
                    <x-ui.field label="Method" name="payMethod" required>
                        <x-ui.select wire:model="payMethod" :options="$methods" />
                    </x-ui.field>
                </div>
                <x-ui.field label="Reference number" name="payReferenceNumber">
                    <x-ui.input wire:model="payReferenceNumber" placeholder="Bank transaction ref" />
                </x-ui.field>
                <x-ui.field label="Notes" name="payNotes">
                    <x-ui.textarea wire:model="payNotes" rows="2" />
                </x-ui.field>
                <div class="flex justify-end gap-2 pt-1">
                    <x-ui.button type="button" variant="secondary" wire:click="$set('showPaymentModal', false)">Cancel</x-ui.button>
                    <x-ui.button type="submit">Record payment</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
