{{-- Shared line-item editor for quotations & invoices.
     Expects: $prefix (e.g. "form"), $items (array), $preview (subtotal/discount/tax/total), $currency --}}
@php $currency = $currency ?? 'RM'; @endphp

<div class="space-y-2">
    <div class="hidden gap-2 px-1 text-xs font-semibold uppercase tracking-wide text-slate-400 sm:grid sm:grid-cols-[1fr_5rem_8rem_8rem_2rem]">
        <span>Description</span><span class="text-right">Qty</span><span class="text-right">Unit price</span><span class="text-right">Amount</span><span></span>
    </div>

    @foreach ($items as $i => $item)
        <div wire:key="{{ $prefix }}-item-{{ $i }}" class="grid grid-cols-1 gap-2 rounded-lg border border-slate-200 p-2 sm:grid-cols-[1fr_5rem_8rem_8rem_2rem] sm:items-center sm:border-0 sm:p-0">
            <div>
                <input type="text" wire:model.blur="{{ $prefix }}.items.{{ $i }}.description" placeholder="Line item"
                       class="w-full rounded-lg border-0 py-1.5 px-2.5 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
                @error("{$prefix}.items.{$i}.description")<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="{{ $prefix }}.items.{{ $i }}.quantity"
                   class="w-full rounded-lg border-0 py-1.5 px-2.5 text-right text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="{{ $prefix }}.items.{{ $i }}.unit_price"
                   class="w-full rounded-lg border-0 py-1.5 px-2.5 text-right text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <div class="text-right text-sm font-medium tabular-nums text-slate-700">
                {{ number_format((float) ($item['quantity'] ?: 0) * (float) ($item['unit_price'] ?: 0), 2) }}
            </div>
            <button type="button" wire:click="removeLine({{ $i }})" class="justify-self-end text-slate-300 hover:text-red-500" title="Remove line">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    @endforeach

    @error("{$prefix}.items")<p class="text-xs text-red-600">{{ $message }}</p>@enderror

    <button type="button" wire:click="addLine"
            class="mt-1 inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-brand-600 hover:bg-brand-50">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
        Add line
    </button>
</div>

{{-- Totals --}}
<div class="mt-4 flex justify-end">
    <dl class="w-full max-w-xs space-y-1.5 text-sm">
        <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="tabular-nums text-slate-800">{{ $currency }} {{ number_format($preview['subtotal'], 2) }}</dd></div>
        <div class="flex items-center justify-between gap-2">
            <dt class="text-slate-500">Discount</dt>
            <dd class="flex items-center gap-1.5">
                <select wire:model.live="{{ $prefix }}.discount_type" class="rounded-md border-0 py-1 pl-2 pr-6 text-xs shadow-sm ring-1 ring-inset ring-slate-300">
                    <option value="amount">{{ $currency }}</option>
                    <option value="percent">%</option>
                </select>
                <input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="{{ $prefix }}.discount_value"
                       class="w-20 rounded-md border-0 py-1 px-2 text-right text-xs shadow-sm ring-1 ring-inset ring-slate-300">
                <span class="w-20 text-right tabular-nums text-slate-800">−{{ number_format($preview['discount'], 2) }}</span>
            </dd>
        </div>
        <div class="flex items-center justify-between gap-2">
            <dt class="text-slate-500">Tax</dt>
            <dd class="flex items-center gap-1.5">
                <input type="number" step="0.01" min="0" max="100" wire:model.live.debounce.400ms="{{ $prefix }}.tax_rate"
                       class="w-20 rounded-md border-0 py-1 px-2 text-right text-xs shadow-sm ring-1 ring-inset ring-slate-300">
                <span class="text-xs text-slate-400">%</span>
                <span class="w-20 text-right tabular-nums text-slate-800">{{ number_format($preview['tax'], 2) }}</span>
            </dd>
        </div>
        <div class="flex justify-between border-t border-slate-200 pt-1.5 text-base font-semibold">
            <dt class="text-slate-900">Total</dt><dd class="tabular-nums text-slate-900">{{ $currency }} {{ number_format($preview['total'], 2) }}</dd>
        </div>
    </dl>
</div>
