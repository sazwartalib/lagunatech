@php
    use Illuminate\Support\Number;
    use App\Enums\QuotationStatus;
@endphp

<div>
    <h1 class="mb-6 text-xl font-semibold tracking-tight text-slate-900">Quotations</h1>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-2 lg:col-span-1">
            @forelse ($quotations as $quotation)
                <button wire:click="view({{ $quotation->id }})" wire:key="q-{{ $quotation->id }}"
                    @class([
                        'w-full rounded-xl border p-3 text-left transition',
                        'border-brand-400 bg-brand-50/60' => $selected?->id === $quotation->id,
                        'border-slate-200 bg-white hover:border-slate-300' => $selected?->id !== $quotation->id,
                    ])>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-800">{{ $quotation->reference }}</span>
                        <x-ui.badge :color="$quotation->status->color()" size="xs">{{ $quotation->status->label() }}</x-ui.badge>
                    </div>
                    <p class="mt-0.5 text-xs text-slate-400">{{ $quotation->title }}</p>
                    <p class="mt-1 text-sm font-semibold tabular-nums text-slate-700">RM {{ Number::format((float) $quotation->total, 2) }}</p>
                </button>
            @empty
                <x-ui.empty-state icon="📝" title="No quotations" description="Proposals from {{ config('app.name') }} will appear here." />
            @endforelse
        </div>

        <div class="lg:col-span-2">
            @if ($selected)
                <x-ui.card>
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $selected->reference }}</h2>
                            <p class="text-sm text-slate-500">{{ $selected->title }}</p>
                        </div>
                        <x-ui.badge :color="$selected->status->color()" size="md" dot>{{ $selected->status->label() }}</x-ui.badge>
                    </div>

                    <table class="mt-5 min-w-full text-sm">
                        <thead><tr class="border-b border-slate-200 text-xs uppercase text-slate-400">
                            <th class="py-2 text-left font-semibold">Description</th>
                            <th class="py-2 text-right font-semibold">Qty</th>
                            <th class="py-2 text-right font-semibold">Unit</th>
                            <th class="py-2 text-right font-semibold">Amount</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($selected->items as $item)
                                <tr wire:key="qi-{{ $item->id }}">
                                    <td class="py-2 text-slate-700">{{ $item->description }}</td>
                                    <td class="py-2 text-right tabular-nums text-slate-600">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                                    <td class="py-2 text-right tabular-nums text-slate-600">{{ Number::format((float) $item->unit_price, 2) }}</td>
                                    <td class="py-2 text-right tabular-nums font-medium text-slate-800">{{ Number::format((float) $item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3 flex justify-end">
                        <dl class="w-full max-w-xs space-y-1 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="tabular-nums">RM {{ Number::format((float) $selected->subtotal, 2) }}</dd></div>
                            @if ((float) $selected->discount_amount > 0)<div class="flex justify-between"><dt class="text-slate-500">Discount</dt><dd class="tabular-nums">− RM {{ Number::format((float) $selected->discount_amount, 2) }}</dd></div>@endif
                            @if ((float) $selected->tax_amount > 0)<div class="flex justify-between"><dt class="text-slate-500">Tax</dt><dd class="tabular-nums">RM {{ Number::format((float) $selected->tax_amount, 2) }}</dd></div>@endif
                            <div class="flex justify-between border-t border-slate-200 pt-1 text-base font-semibold"><dt>Total</dt><dd class="tabular-nums">RM {{ Number::format((float) $selected->total, 2) }}</dd></div>
                        </dl>
                    </div>

                    @if ($selected->terms)
                        <div class="mt-5 border-t border-slate-100 pt-3">
                            <p class="text-xs font-semibold uppercase text-slate-400">Terms</p>
                            <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $selected->terms }}</p>
                        </div>
                    @endif

                    @if (in_array($selected->status, [QuotationStatus::Sent, QuotationStatus::Viewed], true))
                        <div class="mt-5 flex gap-2 border-t border-slate-100 pt-4">
                            <x-ui.button wire:click="decide('approved')" wire:confirm="Approve this quotation? {{ config('app.name') }} will start work.">Approve</x-ui.button>
                            <x-ui.button variant="danger-ghost" wire:click="decide('rejected')" wire:confirm="Reject this quotation?">Reject</x-ui.button>
                        </div>
                    @elseif ($selected->status === QuotationStatus::Approved)
                        <p class="mt-5 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-800">You approved this quotation. Thank you!</p>
                    @endif
                </x-ui.card>
            @else
                <x-ui.empty-state icon="👈" title="Select a quotation" description="Choose a quotation to review and approve." />
            @endif
        </div>
    </div>
</div>
