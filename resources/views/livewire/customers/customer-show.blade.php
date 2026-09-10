@php
    use Illuminate\Support\Number;
    $c = $customer;
    $tabs = [
        'overview' => 'Overview',
        'projects' => 'Projects',
        'contacts' => 'Contacts',
        'communication' => 'Communication',
        'activity' => 'Activity',
    ];
@endphp

<div>
    <x-ui.page-header :title="$c->company_name" :subtitle="$c->reference">
        <x-slot:breadcrumbs>
            <a href="{{ route('customers.index') }}" wire:navigate class="hover:text-slate-600">Customers</a>
            <span>/</span>
            <span class="text-slate-500">{{ $c->company_name }}</span>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            @can('update', $c)
                <x-ui.button variant="secondary" href="{{ route('customers.edit', $c) }}" icon="✎">Edit</x-ui.button>
            @endcan
            @can('create', App\Models\Project::class)
                <x-ui.button href="{{ route('projects.create', ['customer' => $c->id]) }}" icon="＋">New Project</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Summary strip --}}
    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-5">
        <x-ui.stat-card label="Projects" :value="$c->projects->count()" />
        <x-ui.stat-card label="Total value" :value="'RM ' . Number::format($projectValue)" />
        <x-ui.stat-card label="Invoiced" :value="'RM ' . Number::format($financials['invoiced'])" />
        <x-ui.stat-card label="Paid" :value="'RM ' . Number::format($financials['paid'])" tone="green" />
        <x-ui.stat-card label="Outstanding" :value="'RM ' . Number::format($financials['outstanding'])"
            :tone="$financials['outstanding'] > 0 ? 'amber' : 'green'" />
    </div>

    {{-- Tabs --}}
    <div class="mb-4 border-b border-slate-200">
        <nav class="-mb-px flex gap-1 overflow-x-auto">
            @foreach ($tabs as $key => $label)
                <button wire:click="$set('tab', '{{ $key }}')"
                    @class([
                        'whitespace-nowrap border-b-2 px-3 py-2.5 text-sm font-medium transition',
                        'border-brand-600 text-brand-700' => $tab === $key,
                        'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' => $tab !== $key,
                    ])>{{ $label }}</button>
            @endforeach
        </nav>
    </div>

    {{-- Panels --}}
    @if ($tab === 'overview')
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-5">
                <x-ui.card title="Company details">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2 text-sm">
                        <div><dt class="text-slate-400">Contact person</dt><dd class="text-slate-800">{{ $c->contact_person ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Account manager</dt><dd class="text-slate-800">{{ $c->accountManager?->name ?? '—' }}</dd></div>
                        <div><dt class="text-slate-400">Email</dt><dd class="text-slate-800">{{ $c->email ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Phone</dt><dd class="text-slate-800">{{ $c->phone ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Registration no.</dt><dd class="text-slate-800">{{ $c->registration_number ?: '—' }}</dd></div>
                        <div><dt class="text-slate-400">Since</dt><dd class="text-slate-800">{{ $c->created_at->format('d M Y') }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-400">Address</dt><dd class="whitespace-pre-line text-slate-800">{{ $c->address ?: '—' }}</dd></div>
                    </dl>
                </x-ui.card>

                @if ($c->notes)
                    <x-ui.card title="Internal notes">
                        <p class="whitespace-pre-line text-sm text-slate-700">{{ $c->notes }}</p>
                    </x-ui.card>
                @endif
            </div>

            <x-ui.card title="Recent projects">
                @forelse ($c->projects->take(5) as $project)
                    <a href="{{ route('projects.show', $project) }}" wire:navigate wire:key="op-{{ $project->id }}"
                       class="-mx-2 block rounded-lg px-2 py-2 hover:bg-slate-50">
                        <div class="flex items-center justify-between gap-2">
                            <span class="truncate text-sm font-medium text-slate-800">{{ $project->name }}</span>
                            <x-ui.badge :color="$project->status->color()" size="xs">{{ $project->status->label() }}</x-ui.badge>
                        </div>
                        <x-ui.progress :value="$project->progress" show-label size="sm" class="mt-1.5" />
                    </a>
                @empty
                    <p class="text-sm text-slate-400">No projects yet.</p>
                @endforelse
            </x-ui.card>
        </div>
    @elseif ($tab === 'projects')
        <x-ui.card flush>
            <div class="divide-y divide-slate-100">
                @forelse ($c->projects as $project)
                    <a href="{{ route('projects.show', $project) }}" wire:navigate wire:key="cp-{{ $project->id }}"
                       class="flex items-center gap-4 px-4 py-3 hover:bg-slate-50">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="truncate font-medium text-slate-800">{{ $project->name }}</span>
                                <span class="text-xs text-slate-400">{{ $project->reference }}</span>
                            </div>
                            <p class="text-xs text-slate-500">{{ $project->tasks_count }} tasks · Due {{ $project->target_end_date?->format('d M Y') ?? '—' }}</p>
                        </div>
                        <x-ui.badge :color="$project->status->color()" size="xs">{{ $project->status->label() }}</x-ui.badge>
                        <div class="hidden w-28 sm:block"><x-ui.progress :value="$project->progress" show-label size="sm" /></div>
                    </a>
                @empty
                    <div class="p-6">
                        <x-ui.empty-state icon="📁" title="No projects" description="Create a project for this customer.">
                            @can('create', App\Models\Project::class)
                                <x-ui.button href="{{ route('projects.create', ['customer' => $c->id]) }}" icon="＋">New Project</x-ui.button>
                            @endcan
                        </x-ui.empty-state>
                    </div>
                @endforelse
            </div>
        </x-ui.card>
    @elseif ($tab === 'contacts')
        <x-ui.card flush>
            <div class="divide-y divide-slate-100">
                @forelse ($c->contacts as $contact)
                    <div class="flex items-center gap-3 px-4 py-3" wire:key="ct-{{ $contact->id }}">
                        <x-ui.avatar :name="$contact->name" size="sm" />
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-800">
                                {{ $contact->name }}
                                @if ($contact->is_primary)<x-ui.badge color="brand" size="xs">Primary</x-ui.badge>@endif
                            </p>
                            <p class="text-xs text-slate-500">{{ $contact->role ?: '—' }} · {{ $contact->email ?: 'no email' }} · {{ $contact->phone ?: 'no phone' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6"><x-ui.empty-state icon="👤" title="No contacts" description="Contact people for this customer will appear here." /></div>
                @endforelse
            </div>
        </x-ui.card>
    @elseif ($tab === 'communication')
        <x-ui.card flush>
            <x-slot:actions>
                <x-ui.button size="sm" variant="secondary" href="{{ route('communications.index', ['customer' => $c->id]) }}">Open log</x-ui.button>
            </x-slot:actions>
            <div class="divide-y divide-slate-100">
                @forelse ($c->communications as $entry)
                    <div class="flex gap-3 px-4 py-3" wire:key="cc-{{ $entry->id }}">
                        <span class="text-lg">{{ $entry->type->icon() }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-slate-400">
                                <x-ui.badge :color="$entry->type->color()" size="xs">{{ $entry->type->label() }}</x-ui.badge>
                                {{ $entry->communicated_at->format('d M Y, g:ia') }} · {{ $entry->user?->name }}
                            </p>
                            <p class="mt-1 text-sm text-slate-700">{{ $entry->summary }}</p>
                            @if ($entry->action_required)
                                <p class="mt-1 text-xs"><span class="font-medium text-amber-700">Action:</span> {{ $entry->action_required }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6"><x-ui.empty-state icon="💬" title="No communication logged" description="WhatsApp, calls, emails and meetings will show here." /></div>
                @endforelse
            </div>
        </x-ui.card>
    @elseif ($tab === 'activity')
        <x-ui.card>
            <ol class="space-y-4">
                @forelse ($activities as $entry)
                    <li class="flex gap-3" wire:key="ca-{{ $entry->id }}">
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
