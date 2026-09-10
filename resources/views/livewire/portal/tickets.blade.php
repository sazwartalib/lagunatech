<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-semibold tracking-tight text-slate-900">Support</h1>
        <x-ui.button wire:click="$toggle('showForm')" icon="＋">New request</x-ui.button>
    </div>

    @if ($showForm)
        <x-ui.card title="New support request" class="mb-5">
            <form wire:submit="create" class="space-y-3">
                <x-ui.field label="Subject" name="subject" required>
                    <x-ui.input wire:model="subject" />
                </x-ui.field>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-ui.field label="Related project" name="project_id">
                        <x-ui.select wire:model="project_id" placeholder="General enquiry">
                            @foreach ($projects as $project)<option value="{{ $project->id }}">{{ $project->name }}</option>@endforeach
                        </x-ui.select>
                    </x-ui.field>
                    <x-ui.field label="Priority" name="priority" required>
                        <x-ui.select wire:model="priority" :options="App\Enums\Priority::options()" />
                    </x-ui.field>
                </div>
                <x-ui.field label="Describe the issue" name="body" required>
                    <x-ui.textarea wire:model="body" rows="4" />
                </x-ui.field>
                <div class="flex justify-end gap-2">
                    <x-ui.button type="button" variant="secondary" wire:click="$set('showForm', false)">Cancel</x-ui.button>
                    <x-ui.button type="submit">Submit request</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-2">
            @forelse ($tickets as $ticket)
                <button wire:click="select({{ $ticket->id }})" wire:key="t-{{ $ticket->id }}"
                    @class([
                        'w-full rounded-xl border p-3 text-left transition',
                        'border-brand-400 bg-brand-50/60' => $selected?->id === $ticket->id,
                        'border-slate-200 bg-white hover:border-slate-300' => $selected?->id !== $ticket->id,
                    ])>
                    <div class="flex items-center justify-between gap-2">
                        <span class="truncate text-sm font-medium text-slate-800">{{ $ticket->subject }}</span>
                        <x-ui.badge :color="$ticket->status->color()" size="xs">{{ $ticket->status->label() }}</x-ui.badge>
                    </div>
                    <p class="mt-0.5 text-xs text-slate-400">{{ $ticket->reference }} · {{ $ticket->created_at->diffForHumans() }}</p>
                </button>
            @empty
                <x-ui.empty-state icon="🎫" title="No requests yet" description="Submit a request and our team will get back to you." />
            @endforelse
        </div>

        <div class="lg:col-span-2">
            @if ($selected)
                <x-ui.card>
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">{{ $selected->subject }}</h2>
                            <p class="text-xs text-slate-400">{{ $selected->reference }}</p>
                        </div>
                        <x-ui.badge :color="$selected->status->color()" size="md" dot>{{ $selected->status->label() }}</x-ui.badge>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p class="mb-1 text-xs text-slate-400">{{ $selected->created_at->format('d M Y, g:ia') }}</p>
                            <p class="whitespace-pre-line text-sm text-slate-700">{{ $selected->description }}</p>
                        </div>
                        @foreach ($selected->replies as $rep)
                            <div @class([
                                'rounded-lg p-3',
                                'bg-brand-50/60' => $rep->from_customer,
                                'bg-slate-50' => ! $rep->from_customer,
                            ]) wire:key="rr-{{ $rep->id }}">
                                <p class="mb-1 text-xs text-slate-400">
                                    <span class="font-medium text-slate-600">{{ $rep->from_customer ? 'You' : config('app.name') }}</span>
                                    · {{ $rep->created_at->diffForHumans() }}
                                </p>
                                <p class="whitespace-pre-line text-sm text-slate-700">{{ $rep->body }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if ($selected->status->isOpen())
                        <form wire:submit="postReply" class="mt-4 border-t border-slate-100 pt-4">
                            <x-ui.field name="reply">
                                <x-ui.textarea wire:model="reply" rows="3" placeholder="Add a reply…" />
                            </x-ui.field>
                            <div class="mt-2 flex justify-end"><x-ui.button type="submit" size="sm">Send reply</x-ui.button></div>
                        </form>
                    @else
                        <p class="mt-4 border-t border-slate-100 pt-4 text-sm text-slate-400">This ticket is {{ $selected->status->label() }}.</p>
                    @endif
                </x-ui.card>
            @else
                <x-ui.empty-state icon="👈" title="Select a request" description="Pick a request to see the conversation." />
            @endif
        </div>
    </div>
</div>
