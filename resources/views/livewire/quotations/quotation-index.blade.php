@php use Illuminate\Support\Number; @endphp

<div>
    <x-ui.page-header title="Quotations" subtitle="Proposals sent to customers.">
        <x-slot:actions>
            @can('create', App\Models\Quotation::class)
                <x-ui.button href="{{ route('quotations.create') }}" icon="＋">New Quotation</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <x-ui.stat-card label="Pending" :value="$totals['pending']" icon="⏳" tone="amber" />
        <x-ui.stat-card label="Pending value" :value="'RM ' . Number::format($totals['pending_value'])" />
        <x-ui.stat-card label="Approved value" :value="'RM ' . Number::format($totals['approved_value'])" tone="green" />
    </div>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search reference, title, customer…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
        <select wire:model.live="status" class="rounded-lg border-0 py-2 pl-3 pr-9 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600 sm:w-44">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
    </div>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Reference</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Customer</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Status</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Valid until</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($quotations as $quotation)
                        <tr wire:key="qt-{{ $quotation->id }}" class="cursor-pointer hover:bg-slate-50"
                            onclick="window.location='{{ route('quotations.show', $quotation) }}'">
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-800">{{ $quotation->reference }}</span>
                                <span class="block text-xs text-slate-400">{{ $quotation->title }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $quotation->customer->company_name }}</td>
                            <td class="px-4 py-3"><x-ui.badge :color="$quotation->status->color()" size="xs">{{ $quotation->status->label() }}</x-ui.badge></td>
                            <td class="px-4 py-3 text-sm tabular-nums {{ $quotation->isExpired() ? 'text-amber-600' : 'text-slate-600' }}">
                                {{ $quotation->valid_until?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-medium tabular-nums text-slate-800">RM {{ Number::format((float) $quotation->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10">
                                <x-ui.empty-state icon="📝" title="No quotations"
                                    description="{{ $hasFilters ? 'Try adjusting your filters.' : 'Create a quotation to send your first proposal.' }}">
                                    @can('create', App\Models\Quotation::class)
                                        @unless ($hasFilters)<x-ui.button href="{{ route('quotations.create') }}" icon="＋">New Quotation</x-ui.button>@endunless
                                    @endcan
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($quotations->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $quotations->links() }}</div>
        @endif
    </x-ui.card>
</div>
