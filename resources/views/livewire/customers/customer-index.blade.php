<div>
    <x-ui.page-header title="Customers" subtitle="Companies {{ config('app.name') }} works with.">
        <x-slot:actions>
            @can('create', App\Models\Customer::class)
                <x-ui.button href="{{ route('customers.create') }}" icon="＋">New Customer</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filter bar --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search company, reference, contact…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
        <select wire:model.live="status" class="rounded-lg border-0 py-2 pl-3 pr-9 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600 sm:w-44">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @if ($hasFilters)
            <button wire:click="clearFilters" class="text-sm font-medium text-slate-500 hover:text-slate-800">Clear</button>
        @endif
    </div>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <x-ui.sort-header column="company_name" :sort="$sort" :direction="$direction">Company</x-ui.sort-header>
                        <x-ui.sort-header column="reference" :sort="$sort" :direction="$direction">Reference</x-ui.sort-header>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Contact</th>
                        <x-ui.sort-header column="status" :sort="$sort" :direction="$direction">Status</x-ui.sort-header>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Account manager</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500">Projects</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr wire:key="cust-{{ $customer->id }}" class="cursor-pointer hover:bg-slate-50"
                            onclick="window.location='{{ route('customers.show', $customer) }}'">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-ui.avatar :name="$customer->company_name" size="sm" />
                                    <span class="font-medium text-slate-800">{{ $customer->company_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm tabular-nums text-slate-500">{{ $customer->reference }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $customer->contact_person ?: '—' }}
                                @if ($customer->email)<span class="block text-xs text-slate-400">{{ $customer->email }}</span>@endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="$customer->status->color()" dot>{{ $customer->status->label() }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $customer->accountManager?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-sm font-medium tabular-nums text-slate-700">{{ $customer->projects_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10">
                                <x-ui.empty-state icon="🏢" title="No customers found"
                                    description="{{ $hasFilters ? 'Try adjusting your filters.' : 'Add your first customer to get started.' }}">
                                    @can('create', App\Models\Customer::class)
                                        @unless ($hasFilters)
                                            <x-ui.button href="{{ route('customers.create') }}" icon="＋">New Customer</x-ui.button>
                                        @endunless
                                    @endcan
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $customers->links() }}</div>
        @endif
    </x-ui.card>
</div>
