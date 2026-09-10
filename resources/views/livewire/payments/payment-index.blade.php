@php
    use Illuminate\Support\Number;
    use App\Enums\PaymentMethod;
@endphp

<div>
    <x-ui.page-header title="Payments" subtitle="Every payment received, newest first." />

    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <x-ui.stat-card label="This month" :value="'RM ' . Number::format($thisMonth)" icon="💰" tone="green" />
        <x-ui.stat-card label="This year" :value="'RM ' . Number::format($thisYear)" />
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">By method</p>
            <div class="mt-2 space-y-1">
                @forelse ($byMethod as $method => $total)
                    <div class="flex justify-between text-sm" wire:key="bm-{{ $method }}">
                        <span class="text-slate-500">{{ PaymentMethod::from($method)->label() }}</span>
                        <span class="tabular-nums text-slate-800">RM {{ Number::format((float) $total) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No payments yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="relative max-w-md">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search payment, invoice, customer…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
    </div>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Reference</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Invoice</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Customer</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Method</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Date</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $payment)
                        <tr wire:key="p-{{ $payment->id }}" class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm tabular-nums text-slate-500">{{ $payment->reference }}</td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('invoices.show', $payment->invoice_id) }}" wire:navigate class="font-medium text-brand-600 hover:underline">{{ $payment->invoice->reference }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->invoice->customer->company_name }}</td>
                            <td class="px-4 py-3"><x-ui.badge :color="$payment->method->color()" size="xs">{{ $payment->method->label() }}</x-ui.badge></td>
                            <td class="px-4 py-3 text-sm tabular-nums text-slate-600">{{ $payment->paid_on->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-medium tabular-nums text-green-700">RM {{ Number::format((float) $payment->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <x-ui.empty-state icon="💳" title="No payments" description="Payments recorded against invoices will appear here." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($payments->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $payments->links() }}</div>
        @endif
    </x-ui.card>
</div>
