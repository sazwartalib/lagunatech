<div>
    <x-ui.page-header title="Communication Log" subtitle="Every call, WhatsApp, email and meeting with customers.">
        <x-slot:actions>
            @can('create', App\Models\Communication::class)
                <x-ui.button wire:click="$toggle('showForm')" icon="＋">Log communication</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    @if ($pendingFollowUps > 0)
        <div class="mb-4 rounded-lg bg-amber-50 px-4 py-2.5 text-sm text-amber-800 ring-1 ring-inset ring-amber-600/20">
            {{ $pendingFollowUps }} {{ Str::plural('item', $pendingFollowUps) }} need follow-up.
        </div>
    @endif

    @if ($showForm)
        <x-ui.card title="New entry" class="mb-5">
            <form wire:submit="save" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.field label="Customer" name="customer_id" required>
                    <x-ui.select wire:model="customer_id" placeholder="Select customer">
                        @foreach ($customers as $c)<option value="{{ $c->id }}">{{ $c->company_name }}</option>@endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Project (optional)" name="project_id">
                    <x-ui.select wire:model="project_id" placeholder="—">
                        @foreach ($projects as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Type" name="type" required>
                    <x-ui.select wire:model="type" :options="$types" />
                </x-ui.field>
                <x-ui.field label="When" name="communicated_at" required>
                    <x-ui.input type="datetime-local" wire:model="communicated_at" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.field label="Summary" name="summary" required>
                        <x-ui.textarea wire:model="summary" rows="2" placeholder="What was discussed?" />
                    </x-ui.field>
                </div>
                <div class="sm:col-span-2">
                    <x-ui.field label="Action required" name="action_required">
                        <x-ui.input wire:model="action_required" placeholder="e.g. Create change request" />
                    </x-ui.field>
                </div>
                <x-ui.field label="Follow up on" name="follow_up_on">
                    <x-ui.input type="date" wire:model="follow_up_on" />
                </x-ui.field>
                <div class="flex items-end justify-end gap-2">
                    <x-ui.button type="button" variant="secondary" wire:click="$set('showForm', false)">Cancel</x-ui.button>
                    <x-ui.button type="submit">Save entry</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    @endif

    <div class="mb-4 flex flex-wrap gap-2">
        <select wire:model.live="customerFilter" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">All customers</option>
            @foreach ($customers as $c)<option value="{{ $c->id }}">{{ $c->company_name }}</option>@endforeach
        </select>
        <select wire:model.live="typeFilter" class="rounded-lg border-0 py-2 pl-3 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
            <option value="">Any type</option>
            @foreach ($types as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" wire:model.live="followUpOnly" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600"> Needs follow-up
        </label>
    </div>

    <div class="space-y-3">
        @forelse ($communications as $entry)
            <div class="rounded-xl border border-slate-200 bg-white p-4" wire:key="comm-{{ $entry->id }}">
                <div class="flex items-start gap-3">
                    <span class="text-xl">{{ $entry->type->icon() }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
                            <span class="font-medium text-slate-800">{{ $entry->customer->company_name }}</span>
                            <x-ui.badge :color="$entry->type->color()" size="xs">{{ $entry->type->label() }}</x-ui.badge>
                            @if ($entry->project)<span class="text-xs text-slate-400">{{ $entry->project->name }}</span>@endif
                            <span class="text-xs text-slate-400">· {{ $entry->communicated_at->format('d M Y, g:ia') }} · {{ $entry->user?->name }}</span>
                        </div>
                        <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $entry->summary }}</p>
                        @if ($entry->action_required)
                            <div class="mt-2 flex items-center gap-2 text-sm">
                                <span class="rounded bg-amber-50 px-1.5 py-0.5 text-xs font-medium text-amber-800">Action</span>
                                <span class="text-slate-600">{{ $entry->action_required }}</span>
                                @if ($entry->follow_up_on)
                                    <span class="text-xs text-slate-400">by {{ $entry->follow_up_on->format('d M') }}</span>
                                @endif
                                @can('update', $entry)
                                    <button wire:click="toggleFollowUp({{ $entry->id }})"
                                        class="ml-auto text-xs font-medium {{ $entry->follow_up_done ? 'text-green-600' : 'text-slate-400 hover:text-slate-700' }}">
                                        {{ $entry->follow_up_done ? '✓ Done' : 'Mark done' }}
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <x-ui.empty-state icon="💬" title="No communication logged"
                description="Keep a record of what was said, so nothing slips through WhatsApp." />
        @endforelse
    </div>

    @if ($communications->hasPages())
        <div class="mt-4">{{ $communications->links() }}</div>
    @endif
</div>
