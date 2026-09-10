@php
    use Illuminate\Support\Number;
    use App\Enums\Priority;
    $p = $project;
    $tabs = [
        'overview' => 'Overview',
        'tasks' => 'Tasks',
        'milestones' => 'Milestones',
        'bugs' => 'Bugs',
        'change_requests' => 'Change Requests',
        'meetings' => 'Meetings',
        'documents' => 'Documents',
        'support' => 'Support',
        'maintenance' => 'Maintenance',
        'finance' => 'Quotations & Invoices',
        'notes' => 'Notes',
        'activity' => 'Activity',
    ];
    $milestoneRollup = $p->milestones->count() > 0 ? (int) round($p->milestones->avg('progress')) : $p->progress;
@endphp

<div>
    <x-ui.page-header :title="$p->name" :subtitle="$p->reference">
        <x-slot:breadcrumbs>
            <a href="{{ route('projects.index') }}" wire:navigate class="hover:text-slate-600">Projects</a>
            <span>/</span>
            <span class="text-slate-500">{{ $p->name }}</span>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            @can('update', $p)
                <x-ui.button variant="secondary" href="{{ route('projects.edit', $p) }}" icon="✎">Edit</x-ui.button>
            @endcan
            @can('manageTasks', $p)
                <x-ui.button wire:click="$set('tab', 'tasks')" icon="＋">Add Task</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Key facts --}}
    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        <div class="rounded-xl border border-slate-200 bg-white p-3">
            <p class="text-[11px] font-medium uppercase text-slate-400">Customer</p>
            <a href="{{ route('customers.show', $p->customer) }}" wire:navigate class="text-sm font-medium text-brand-700 hover:underline">{{ $p->customer->company_name }}</a>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3">
            <p class="text-[11px] font-medium uppercase text-slate-400">Status</p>
            <x-ui.badge :color="$p->status->color()" size="xs">{{ $p->status->label() }}</x-ui.badge>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3">
            <p class="text-[11px] font-medium uppercase text-slate-400">Health</p>
            <x-ui.badge :color="$p->health->color()" size="xs" dot>{{ $p->health->label() }}</x-ui.badge>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3">
            <p class="text-[11px] font-medium uppercase text-slate-400">Progress</p>
            <p class="text-sm font-semibold text-slate-800">{{ $p->progress }}%</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3">
            <p class="text-[11px] font-medium uppercase text-slate-400">Due</p>
            <p class="text-sm font-semibold {{ $p->is_overdue ? 'text-red-600' : 'text-slate-800' }}">{{ $p->target_end_date?->format('d M Y') ?? '—' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-3">
            <p class="text-[11px] font-medium uppercase text-slate-400">PIC</p>
            <p class="text-sm font-semibold text-slate-800">{{ $p->lead?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="mb-4 border-b border-slate-200">
        <nav class="-mb-px flex gap-1 overflow-x-auto">
            @foreach ($tabs as $key => $label)
                <button wire:click="$set('tab', '{{ $key }}')"
                    @class([
                        'whitespace-nowrap border-b-2 px-3 py-2.5 text-sm font-medium transition',
                        'border-brand-600 text-brand-700' => $tab === $key,
                        'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' => $tab !== $key,
                    ])>
                    {{ $label }}
                    @if ($key === 'tasks')<span class="ml-1 rounded-full bg-slate-100 px-1.5 text-xs text-slate-500">{{ $p->tasks->count() }}</span>@endif
                </button>
            @endforeach
        </nav>
    </div>

    {{-- ===================== OVERVIEW ===================== --}}
    @if ($tab === 'overview')
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="space-y-5 lg:col-span-2">
                <x-ui.card title="Description">
                    <p class="whitespace-pre-line text-sm text-slate-700">{{ $p->description ?: 'No description yet.' }}</p>
                </x-ui.card>

                <x-ui.card title="Milestones" subtitle="Rolled-up progress {{ $milestoneRollup }}%">
                    @forelse ($p->milestones as $milestone)
                        <div class="py-2" wire:key="ov-ms-{{ $milestone->id }}">
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="text-slate-700">{{ $milestone->name }}</span>
                                <span class="font-medium tabular-nums text-slate-500">{{ $milestone->progress }}%</span>
                            </div>
                            <x-ui.progress :value="$milestone->progress"
                                :color="$milestone->progress === 100 ? 'green' : 'brand'" size="md" />
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No milestones defined.</p>
                    @endforelse
                </x-ui.card>
            </div>

            <div class="space-y-5">
                <x-ui.card title="Details">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-400">Type</dt><dd class="text-slate-800">{{ $p->type ?: '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Technology</dt><dd class="text-slate-800">{{ $p->technology ?: '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Priority</dt><dd><x-ui.badge :color="$p->priority->color()" size="xs">{{ $p->priority->label() }}</x-ui.badge></dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Start</dt><dd class="text-slate-800">{{ $p->start_date?->format('d M Y') ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Budget</dt><dd class="text-slate-800">{{ $p->budget ? 'RM ' . Number::format((float) $p->budget) : '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Value</dt><dd class="font-medium text-slate-800">{{ $p->value ? 'RM ' . Number::format((float) $p->value) : '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Customer PIC</dt><dd class="text-slate-800">{{ $p->customer_pic_name ?: '—' }}</dd></div>
                    </dl>
                </x-ui.card>

                <x-ui.card title="Team">
                    <div class="flex flex-wrap gap-2">
                        @forelse ($p->members as $member)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 py-1 pl-1 pr-2.5 text-xs" wire:key="mm-{{ $member->id }}">
                                <x-ui.avatar :name="$member->name" size="xs" /> {{ $member->name }}
                            </span>
                        @empty
                            <p class="text-sm text-slate-400">No members assigned.</p>
                        @endforelse
                    </div>
                </x-ui.card>
            </div>
        </div>

    {{-- ===================== TASKS BOARD ===================== --}}
    @elseif ($tab === 'tasks')
        @can('manageTasks', $p)
            <form wire:submit="addTask" class="mb-4 flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-ui.field label="New task" name="newTaskTitle">
                        <x-ui.input wire:model="newTaskTitle" placeholder="e.g. Build login screen" />
                    </x-ui.field>
                </div>
                <div class="w-full sm:w-40">
                    <x-ui.field label="Assignee" name="newTaskAssignee">
                        <x-ui.select wire:model="newTaskAssignee" placeholder="—">
                            @foreach ($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}</option>@endforeach
                        </x-ui.select>
                    </x-ui.field>
                </div>
                <div class="w-full sm:w-32">
                    <x-ui.field label="Priority" name="newTaskPriority">
                        <x-ui.select wire:model="newTaskPriority" :options="Priority::options()" />
                    </x-ui.field>
                </div>
                <div class="w-full sm:w-40">
                    <x-ui.field label="Due" name="newTaskDue">
                        <x-ui.input type="date" wire:model="newTaskDue" />
                    </x-ui.field>
                </div>
                <x-ui.button type="submit">Add</x-ui.button>
            </form>
        @endcan

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($boardColumns as $column)
                @php $columnTasks = $tasksByStatus[$column->value] ?? collect(); @endphp
                <div class="rounded-xl bg-slate-100/70 p-2" wire:key="col-{{ $column->value }}">
                    <div class="mb-2 flex items-center justify-between px-1">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $column->label() }}</span>
                        <span class="rounded-full bg-white px-1.5 text-xs text-slate-500">{{ $columnTasks->count() }}</span>
                    </div>
                    <div class="space-y-2">
                        @forelse ($columnTasks as $task)
                            <div class="rounded-lg border border-slate-200 bg-white p-2.5 shadow-sm" wire:key="task-{{ $task->id }}">
                                <p class="text-sm text-slate-800">{{ $task->title }}</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <x-ui.badge :color="$task->priority->color()" size="xs">{{ $task->priority->label() }}</x-ui.badge>
                                        @if ($task->due_date)
                                            <span class="text-[11px] {{ $task->is_overdue ? 'text-red-600' : 'text-slate-400' }}">{{ $task->due_date->format('d M') }}</span>
                                        @endif
                                    </div>
                                    @if ($task->assignee)<x-ui.avatar :name="$task->assignee->name" size="xs" />@endif
                                </div>
                                @can('manageTasks', $p)
                                    <select
                                        class="mt-2 w-full rounded border-0 bg-slate-50 py-1 text-xs text-slate-600 ring-1 ring-inset ring-slate-200 focus:ring-brand-500"
                                        wire:change="moveTask({{ $task->id }}, $event.target.value)"
                                    >
                                        @foreach ($boardColumns as $opt)
                                            <option value="{{ $opt->value }}" @selected($opt === $task->status)>{{ $opt->label() }}</option>
                                        @endforeach
                                    </select>
                                @endcan
                            </div>
                        @empty
                            <p class="px-1 py-3 text-center text-xs text-slate-400">Empty</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    {{-- ===================== MILESTONES ===================== --}}
    @elseif ($tab === 'milestones')
        @can('update', $p)
            <form wire:submit="addMilestone" class="mb-4 flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-ui.field label="New milestone" name="newMilestoneName">
                        <x-ui.input wire:model="newMilestoneName" placeholder="e.g. UI/UX sign-off" />
                    </x-ui.field>
                </div>
                <div class="w-full sm:w-44">
                    <x-ui.field label="Due" name="newMilestoneDue">
                        <x-ui.input type="date" wire:model="newMilestoneDue" />
                    </x-ui.field>
                </div>
                <x-ui.button type="submit">Add</x-ui.button>
            </form>
        @endcan

        <x-ui.card flush>
            <div class="divide-y divide-slate-100">
                @forelse ($p->milestones as $milestone)
                    <div class="px-4 py-3" wire:key="ms-{{ $milestone->id }}">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $milestone->name }}</p>
                                <p class="text-xs text-slate-400">Due {{ $milestone->due_date?->format('d M Y') ?? '—' }}</p>
                            </div>
                            <x-ui.badge :color="$milestone->status->color()" size="xs">{{ $milestone->status->label() }}</x-ui.badge>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <x-ui.progress :value="$milestone->progress" :color="$milestone->progress === 100 ? 'green' : 'brand'" size="md" />
                            <span class="w-9 shrink-0 text-right text-xs font-medium tabular-nums text-slate-500">{{ $milestone->progress }}%</span>
                        </div>
                        @can('update', $p)
                            <input type="range" min="0" max="100" step="10" value="{{ $milestone->progress }}"
                                   wire:change="setMilestoneProgress({{ $milestone->id }}, $event.target.value)"
                                   class="mt-2 w-full accent-brand-600">
                        @endcan
                    </div>
                @empty
                    <div class="p-6"><x-ui.empty-state icon="◈" title="No milestones" description="Break the project into deliverable stages." /></div>
                @endforelse
            </div>
        </x-ui.card>

    {{-- ===================== BUGS ===================== --}}
    @elseif ($tab === 'bugs')
        <x-ui.card flush>
            <x-slot:actions>
                @can('create', App\Models\Bug::class)
                    <x-ui.button size="sm" variant="secondary" href="{{ route('bugs.create', ['project' => $p->id]) }}" icon="＋">Log bug</x-ui.button>
                @endcan
            </x-slot:actions>
            <div class="divide-y divide-slate-100">
                @forelse ($p->bugs as $bug)
                    <a href="{{ route('bugs.show', $bug) }}" wire:navigate wire:key="pb-{{ $bug->id }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="text-xs tabular-nums text-slate-400">{{ $bug->reference }}</span>
                        <span class="min-w-0 flex-1 truncate text-sm text-slate-800">{{ $bug->title }}</span>
                        <x-ui.badge :color="$bug->priority->color()" size="xs">{{ $bug->priority->label() }}</x-ui.badge>
                        <x-ui.badge :color="$bug->status->color()" size="xs">{{ $bug->status->label() }}</x-ui.badge>
                        @if ($bug->assignee)<x-ui.avatar :name="$bug->assignee->name" size="xs" />@endif
                    </a>
                @empty
                    <div class="p-6"><x-ui.empty-state icon="🐞" title="No bugs" description="No issues logged against this project." /></div>
                @endforelse
            </div>
        </x-ui.card>

    {{-- ===================== CHANGE REQUESTS ===================== --}}
    @elseif ($tab === 'change_requests')
        <x-ui.card flush>
            <x-slot:actions>
                @can('create', App\Models\ChangeRequest::class)
                    <x-ui.button size="sm" variant="secondary" href="{{ route('change-requests.create', ['project' => $p->id]) }}" icon="＋">New CR</x-ui.button>
                @endcan
            </x-slot:actions>
            <div class="divide-y divide-slate-100">
                @forelse ($p->changeRequests as $cr)
                    <a href="{{ route('change-requests.show', $cr) }}" wire:navigate wire:key="pcr-{{ $cr->id }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="text-xs tabular-nums text-slate-400">{{ $cr->reference }}</span>
                        <span class="min-w-0 flex-1 truncate text-sm text-slate-800">{{ $cr->title }}</span>
                        @if ($cr->estimated_cost)<span class="text-sm tabular-nums text-slate-500">RM {{ Number::format((float) $cr->estimated_cost) }}</span>@endif
                        <x-ui.badge :color="$cr->status->color()" size="xs">{{ $cr->status->label() }}</x-ui.badge>
                    </a>
                @empty
                    <div class="p-6"><x-ui.empty-state icon="⇄" title="No change requests" description="Extra scope requests for this project will appear here." /></div>
                @endforelse
            </div>
        </x-ui.card>

    {{-- ===================== MEETINGS ===================== --}}
    @elseif ($tab === 'meetings')
        <x-ui.card flush>
            <x-slot:actions>
                @can('create', App\Models\Meeting::class)
                    <x-ui.button size="sm" variant="secondary" href="{{ route('meetings.create', ['project' => $p->id]) }}" icon="＋">Schedule</x-ui.button>
                @endcan
            </x-slot:actions>
            <div class="divide-y divide-slate-100">
                @forelse ($p->meetings as $meeting)
                    <a href="{{ route('meetings.show', $meeting) }}" wire:navigate wire:key="pm-{{ $meeting->id }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-slate-100 text-center text-[11px] font-semibold text-slate-600">
                            {{ $meeting->scheduled_at->format('d') }}<br>{{ $meeting->scheduled_at->format('M') }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-800">{{ $meeting->title }}</p>
                            <p class="text-xs text-slate-400">{{ $meeting->scheduled_at->format('g:ia') }} · {{ $meeting->duration_minutes }} min</p>
                        </div>
                        <x-ui.badge :color="$meeting->status->color()" size="xs">{{ $meeting->status->label() }}</x-ui.badge>
                    </a>
                @empty
                    <div class="p-6"><x-ui.empty-state icon="🤝" title="No meetings" description="Schedule a meeting for this project." /></div>
                @endforelse
            </div>
        </x-ui.card>

    {{-- ===================== DOCUMENTS ===================== --}}
    @elseif ($tab === 'documents')
        <livewire:documents.document-manager :project="$p" :key="'docs-'.$p->id" />

    {{-- ===================== SUPPORT ===================== --}}
    @elseif ($tab === 'support')
        <x-ui.card flush>
            <x-slot:actions>
                @can('create', App\Models\SupportTicket::class)
                    <x-ui.button size="sm" variant="secondary" href="{{ route('support.create', ['customer' => $p->customer_id, 'project' => $p->id]) }}" icon="＋">New ticket</x-ui.button>
                @endcan
            </x-slot:actions>
            <div class="divide-y divide-slate-100">
                @forelse ($p->supportTickets as $ticket)
                    <a href="{{ route('support.show', $ticket) }}" wire:navigate wire:key="pt-{{ $ticket->id }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="text-xs tabular-nums text-slate-400">{{ $ticket->reference }}</span>
                        <span class="min-w-0 flex-1 truncate text-sm text-slate-800">{{ $ticket->subject }}</span>
                        <x-ui.badge :color="$ticket->status->color()" size="xs">{{ $ticket->status->label() }}</x-ui.badge>
                    </a>
                @empty
                    <div class="p-6"><x-ui.empty-state icon="🎫" title="No tickets" description="Support requests for this project appear here." /></div>
                @endforelse
            </div>
        </x-ui.card>

    {{-- ===================== MAINTENANCE ===================== --}}
    @elseif ($tab === 'maintenance')
        <x-ui.card flush>
            <x-slot:actions>
                @can('create', App\Models\MaintenancePlan::class)
                    <x-ui.button size="sm" variant="secondary" href="{{ route('maintenance.create', ['customer' => $p->customer_id, 'project' => $p->id]) }}" icon="＋">New plan</x-ui.button>
                @endcan
            </x-slot:actions>
            <div class="divide-y divide-slate-100">
                @forelse ($p->maintenancePlans as $plan)
                    <a href="{{ route('maintenance.edit', $plan) }}" wire:navigate wire:key="pmp-{{ $plan->id }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="min-w-0 flex-1 truncate text-sm font-medium text-slate-800">{{ $plan->name }}</span>
                        <span class="text-sm tabular-nums text-slate-500">RM {{ Number::format((float) $plan->fee, 2) }} / {{ $plan->billing_cycle->label() }}</span>
                        <x-ui.badge :color="$plan->status->color()" size="xs">{{ $plan->status->label() }}</x-ui.badge>
                    </a>
                @empty
                    <div class="p-6"><x-ui.empty-state icon="🛠" title="No maintenance plan" description="Set up recurring support for this project." /></div>
                @endforelse
            </div>
        </x-ui.card>

    {{-- ===================== FINANCE ===================== --}}
    @elseif ($tab === 'finance')
        <div class="space-y-5">
            <x-ui.card title="Quotations">
                <x-slot:actions>
                    @can('create', App\Models\Quotation::class)
                        <x-ui.button size="sm" variant="secondary" href="{{ route('quotations.create', ['project' => $p->id]) }}" icon="＋">New</x-ui.button>
                    @endcan
                </x-slot:actions>
                @forelse ($p->quotations as $quotation)
                    <a href="{{ route('quotations.show', $quotation) }}" wire:navigate wire:key="pq-{{ $quotation->id }}"
                       class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-slate-50">
                        <span class="font-medium text-slate-800">{{ $quotation->reference }}</span>
                        <x-ui.badge :color="$quotation->status->color()" size="xs">{{ $quotation->status->label() }}</x-ui.badge>
                        <span class="ml-auto text-sm font-medium tabular-nums text-slate-700">RM {{ Number::format((float) $quotation->total, 2) }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400">No quotations linked to this project.</p>
                @endforelse
            </x-ui.card>

            <x-ui.card title="Invoices">
                <x-slot:actions>
                    @can('create', App\Models\Invoice::class)
                        <x-ui.button size="sm" variant="secondary" href="{{ route('invoices.create', ['project' => $p->id]) }}" icon="＋">New</x-ui.button>
                    @endcan
                </x-slot:actions>
                @forelse ($p->invoices as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate wire:key="pi-{{ $invoice->id }}"
                       class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-slate-50">
                        <span class="font-medium text-slate-800">{{ $invoice->reference }}</span>
                        <x-ui.badge :color="$invoice->status->color()" size="xs">{{ $invoice->status->label() }}</x-ui.badge>
                        <span class="ml-auto text-sm tabular-nums text-slate-500">RM {{ Number::format((float) $invoice->total, 2) }}</span>
                        <span class="w-28 text-right text-sm font-medium tabular-nums {{ $invoice->outstanding > 0 ? 'text-slate-900' : 'text-green-600' }}">
                            RM {{ Number::format($invoice->outstanding, 2) }} due
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400">No invoices for this project yet.</p>
                @endforelse

                @if ($p->invoices->isNotEmpty())
                    <x-slot:footer>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Billed <span class="font-medium text-slate-800">RM {{ Number::format((float) $p->invoices->sum('total'), 2) }}</span></span>
                            <span class="text-slate-500">Outstanding <span class="font-medium text-slate-900">RM {{ Number::format($p->invoices->sum(fn ($i) => $i->outstanding), 2) }}</span></span>
                        </div>
                    </x-slot:footer>
                @endif
            </x-ui.card>
        </div>

    {{-- ===================== NOTES ===================== --}}
    @elseif ($tab === 'notes')
        @can('update', $p)
            <form wire:submit="addNote" class="mb-4 rounded-xl border border-slate-200 bg-white p-3">
                <x-ui.field name="newNote">
                    <x-ui.textarea wire:model="newNote" rows="3" placeholder="Add an internal note…" />
                </x-ui.field>
                <div class="mt-2 flex justify-end">
                    <x-ui.button type="submit" size="sm">Add note</x-ui.button>
                </div>
            </form>
        @endcan

        <div class="space-y-3">
            @forelse ($p->notes as $note)
                <div class="rounded-xl border border-slate-200 bg-white p-3" wire:key="note-{{ $note->id }}">
                    <div class="mb-1 flex items-center gap-2 text-xs text-slate-400">
                        <x-ui.avatar :name="$note->user?->name ?? 'System'" size="xs" />
                        <span class="font-medium text-slate-600">{{ $note->user?->name ?? 'System' }}</span>
                        <span>· {{ $note->created_at->diffForHumans() }}</span>
                        <x-ui.badge color="slate" size="xs">Internal</x-ui.badge>
                    </div>
                    <p class="whitespace-pre-line text-sm text-slate-700">{{ $note->body }}</p>
                </div>
            @empty
                <x-ui.empty-state icon="📝" title="No notes yet" description="Capture decisions, blockers and context here." />
            @endforelse
        </div>

    {{-- ===================== ACTIVITY ===================== --}}
    @elseif ($tab === 'activity')
        <x-ui.card>
            <ol class="space-y-4">
                @forelse ($activities as $entry)
                    <li class="flex gap-3" wire:key="pa-{{ $entry->id }}">
                        <x-ui.avatar :name="$entry->causer?->name ?? 'System'" size="xs" class="mt-0.5" />
                        <div>
                            <p class="text-sm text-slate-700">
                                <span class="font-medium text-slate-900">{{ $entry->causer?->name ?? 'System' }}</span>
                                {{ $entry->description }}
                            </p>
                            <p class="text-xs text-slate-400">{{ $entry->created_at->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">No activity recorded yet.</li>
                @endforelse
            </ol>
        </x-ui.card>
    @endif
</div>
