<div>
    <x-ui.page-header title="Leads" subtitle="Enquiries from the website and other channels." />

    @if ($newCount > 0)
        <div class="mb-4 rounded-lg bg-blue-50 px-4 py-2.5 text-sm text-blue-800 ring-1 ring-inset ring-blue-600/20">
            {{ $newCount }} new {{ Str::plural('lead', $newCount) }} waiting to be contacted.
        </div>
    @endif

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <div class="relative min-w-[12rem] flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, company, email, reference…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
        <select wire:model.live="status" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">Any status</option>
            @foreach ($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" wire:model.live="openOnly" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600"> Open only
        </label>
    </div>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Lead</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Project</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Status</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Owner</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Received</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($leads as $lead)
                        <tr wire:key="lead-{{ $lead->id }}" class="cursor-pointer hover:bg-slate-50"
                            onclick="window.location='{{ route('leads.show', $lead) }}'">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800">{{ $lead->name }}</p>
                                <p class="text-xs text-slate-400">{{ $lead->company ?: 'No company' }} · {{ $lead->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $lead->project_type ?: '—' }}
                                @if ($lead->budget_range)<span class="block text-xs text-slate-400">{{ $lead->budget_range }}</span>@endif
                            </td>
                            <td class="px-4 py-3"><x-ui.badge :color="$lead->status->color()" size="xs" dot>{{ $lead->status->label() }}</x-ui.badge></td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $lead->owner?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm tabular-nums text-slate-500">{{ $lead->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10">
                            <x-ui.empty-state icon="✦" title="No leads yet"
                                description="Enquiries submitted through your website landing page will appear here." />
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($leads->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $leads->links() }}</div>
        @endif
    </x-ui.card>
</div>
