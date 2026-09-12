@php use Illuminate\Support\Number; @endphp

<div>
    <h1 class="text-xl font-semibold tracking-tight text-slate-900">Invoices</h1>

    @if ($outstanding > 0)
        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm text-amber-900">You have <span class="font-semibold">RM {{ Number::format($outstanding, 2) }}</span> outstanding.</p>
            @if ($bankDetails)
                <p class="mt-2 whitespace-pre-line text-xs text-amber-800">{{ $bankDetails }}</p>
            @endif
        </div>
    @endif

    <div class="mt-5 space-y-3">
        @forelse ($invoices as $invoice)
            <div class="rounded-xl border border-slate-200 bg-white p-4" wire:key="i-{{ $invoice->id }}">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $invoice->reference }}
                            <span class="text-xs text-slate-400">· {{ $invoice->title }}</span>
                        </p>
                        <p class="text-xs text-slate-400">Issued {{ $invoice->issue_date->format('d M Y') }} · Due {{ $invoice->due_date->format('d M Y') }}</p>
                    </div>
                    <x-ui.badge :color="$invoice->status->color()" size="sm" dot>{{ $invoice->status->label() }}</x-ui.badge>
                </div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3 text-sm">
                    <div class="flex flex-wrap gap-3 sm:gap-6">
                        <span class="text-slate-500">Total <span class="font-medium tabular-nums text-slate-800">RM {{ Number::format((float) $invoice->total, 2) }}</span></span>
                        <span class="text-slate-500">Paid <span class="font-medium tabular-nums text-green-700">RM {{ Number::format((float) $invoice->amount_paid, 2) }}</span></span>
                        <span class="text-slate-500">Balance <span class="font-semibold tabular-nums text-slate-900">RM {{ Number::format($invoice->outstanding, 2) }}</span></span>
                    </div>
                    <a href="{{ route('portal.invoices.pdf', $invoice) }}" target="_blank"
                       class="text-sm font-medium text-brand-600 hover:text-brand-700">Download PDF</a>
                </div>
            </div>
        @empty
            <x-ui.empty-state icon="🧾" title="No invoices" description="Invoices from {{ config('app.name') }} will appear here." />
        @endforelse
    </div>
</div>
