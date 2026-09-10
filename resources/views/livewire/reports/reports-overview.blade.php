@php use Illuminate\Support\Number; @endphp

<div>
    <x-ui.page-header title="Reports" subtitle="A management snapshot of {{ config('app.name') }}." />

    <div class="space-y-6">
        {{-- Projects --}}
        <x-ui.card title="Projects">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ([
                    ['Total', $projectReport['total'], 'slate'],
                    ['Active', $projectReport['active'], 'brand'],
                    ['Completed', $projectReport['completed'], 'green'],
                    ['Maintenance', $projectReport['maintenance'], 'teal'],
                    ['At risk', $projectReport['at_risk'], 'amber'],
                    ['Overdue', $projectReport['overdue'], 'red'],
                ] as [$label, $value, $tone])
                    <div wire:key="pr-{{ $label }}">
                        <p class="text-xs font-medium uppercase text-slate-400">{{ $label }}</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums text-slate-900">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        {{-- Finance --}}
        <x-ui.card title="Financial">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ([
                    ['Live quotations', $financialReport['quotation_value']],
                    ['Approved quotes', $financialReport['approved_quotations']],
                    ['Invoiced', $financialReport['invoiced']],
                    ['Collected', $financialReport['paid']],
                    ['Outstanding', $financialReport['outstanding']],
                    ['Overdue', $financialReport['overdue']],
                ] as [$label, $value])
                    <div wire:key="fr-{{ $label }}">
                        <p class="text-xs font-medium uppercase text-slate-400">{{ $label }}</p>
                        <p class="mt-1 text-lg font-semibold tabular-nums text-slate-900">RM {{ Number::format($value) }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        {{-- Staff --}}
        <x-ui.card title="Staff workload" flush>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50/70 text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Staff</th>
                            <th class="px-4 py-2.5 text-right">Active projects</th>
                            <th class="px-4 py-2.5 text-right">Open tasks</th>
                            <th class="px-4 py-2.5 text-right">Overdue tasks</th>
                            <th class="px-4 py-2.5 text-right">Completed tasks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($staffReport as $row)
                            <tr wire:key="sr-{{ $loop->index }}">
                                <td class="px-4 py-2.5">
                                    <span class="font-medium text-slate-800">{{ $row['name'] }}</span>
                                    <span class="block text-xs text-slate-400">{{ $row['position'] }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-right tabular-nums text-slate-700">{{ $row['active_projects'] }}</td>
                                <td class="px-4 py-2.5 text-right tabular-nums text-slate-700">{{ $row['open_tasks'] }}</td>
                                <td class="px-4 py-2.5 text-right tabular-nums {{ $row['overdue_tasks'] > 0 ? 'font-medium text-red-600' : 'text-slate-500' }}">{{ $row['overdue_tasks'] }}</td>
                                <td class="px-4 py-2.5 text-right tabular-nums text-slate-500">{{ $row['done_tasks'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <p class="text-xs text-slate-400">Project profitability reporting is planned for a future release.</p>
    </div>
</div>
