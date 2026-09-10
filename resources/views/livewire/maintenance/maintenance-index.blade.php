@php use Illuminate\Support\Number; @endphp

<div>
    <x-ui.page-header title="Maintenance" subtitle="Ongoing support & hosting plans.">
        <x-slot:actions>
            @can('create', App\Models\MaintenancePlan::class)
                <x-ui.button href="{{ route('maintenance.create') }}" icon="＋">New Plan</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <x-ui.stat-card label="Annual recurring" :value="'RM ' . Number::format($annualRecurring)" tone="green" />
        <x-ui.stat-card label="Renewing in 30 days" :value="$renewingSoon" tone="amber" />
        <x-ui.stat-card label="Active plans" :value="$plans->total()" />
    </div>

    <div class="mb-4">
        <select wire:model.live="status" class="rounded-lg border-0 py-2 pl-3 pr-9 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600 sm:w-48">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
    </div>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Plan</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Customer</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Cycle</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500">Fee</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Next renewal</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($plans as $plan)
                        <tr wire:key="mp-{{ $plan->id }}" class="cursor-pointer hover:bg-slate-50"
                            onclick="window.location='{{ route('maintenance.edit', $plan) }}'">
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $plan->name }}
                                @if ($plan->project)<span class="block text-xs text-slate-400">{{ $plan->project->name }}</span>@endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $plan->customer->company_name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $plan->billing_cycle->label() }}</td>
                            <td class="px-4 py-3 text-right text-sm tabular-nums text-slate-700">RM {{ Number::format((float) $plan->fee, 2) }}</td>
                            <td class="px-4 py-3 text-sm tabular-nums text-slate-600">{{ $plan->next_renewal_on?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3"><x-ui.badge :color="$plan->status->color()" size="xs">{{ $plan->status->label() }}</x-ui.badge></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10">
                            <x-ui.empty-state icon="🛠" title="No maintenance plans" description="Track recurring support agreements here.">
                                @can('create', App\Models\MaintenancePlan::class)
                                    <x-ui.button href="{{ route('maintenance.create') }}" icon="＋">New Plan</x-ui.button>
                                @endcan
                            </x-ui.empty-state>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($plans->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $plans->links() }}</div>
        @endif
    </x-ui.card>
</div>
