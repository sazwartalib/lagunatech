@php use Illuminate\Support\Number; @endphp

<div>
    <x-ui.page-header title="Change Requests" subtitle="Extra scope customers ask for mid-project.">
        <x-slot:actions>
            @can('create', App\Models\ChangeRequest::class)
                <x-ui.button href="{{ route('change-requests.create') }}" icon="＋">New Change Request</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    @if ($awaiting > 0)
        <div class="mb-4 rounded-lg bg-amber-50 px-4 py-2.5 text-sm text-amber-800 ring-1 ring-inset ring-amber-600/20">
            {{ $awaiting }} change {{ Str::plural('request', $awaiting) }} awaiting quoting or approval.
        </div>
    @endif

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search reference or title…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
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
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Reference</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Project</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Status</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Assignee</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500">Est. cost</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500">+ Days</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($changeRequests as $cr)
                        <tr wire:key="cr-{{ $cr->id }}" class="cursor-pointer hover:bg-slate-50"
                            onclick="window.location='{{ route('change-requests.show', $cr) }}'">
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-800">{{ $cr->reference }}</span>
                                <span class="block text-xs text-slate-400">{{ $cr->title }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $cr->project->name }}<span class="block text-xs text-slate-400">{{ $cr->customer->company_name }}</span></td>
                            <td class="px-4 py-3"><x-ui.badge :color="$cr->status->color()" size="xs">{{ $cr->status->label() }}</x-ui.badge></td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $cr->assignee?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-sm tabular-nums text-slate-700">{{ $cr->estimated_cost ? 'RM ' . Number::format((float) $cr->estimated_cost) : '—' }}</td>
                            <td class="px-4 py-3 text-right text-sm tabular-nums text-slate-500">{{ $cr->additional_days ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10">
                            <x-ui.empty-state icon="⇄" title="No change requests" description="Log a change request when a customer asks for something beyond the agreed scope.">
                                @can('create', App\Models\ChangeRequest::class)
                                    <x-ui.button href="{{ route('change-requests.create') }}" icon="＋">New Change Request</x-ui.button>
                                @endcan
                            </x-ui.empty-state>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($changeRequests->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $changeRequests->links() }}</div>
        @endif
    </x-ui.card>
</div>
