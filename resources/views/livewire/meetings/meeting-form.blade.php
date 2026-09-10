<div class="mx-auto max-w-2xl">
    <x-ui.page-header :title="$meeting ? 'Edit meeting' : 'Schedule a meeting'">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="$meeting ? route('meetings.show', $meeting) : route('meetings.index')">Cancel</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save" class="space-y-5">
        <x-ui.card title="Details">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-ui.field label="Title" name="form.title" required>
                        <x-ui.input wire:model="form.title" placeholder="e.g. Sprint review" />
                    </x-ui.field>
                </div>
                <x-ui.field label="Project" name="form.project_id">
                    <x-ui.select wire:model="form.project_id" placeholder="—">
                        @foreach ($projects as $project)<option value="{{ $project->id }}">{{ $project->name }}</option>@endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Customer" name="form.customer_id">
                    <x-ui.select wire:model="form.customer_id" placeholder="—">
                        @foreach ($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->company_name }}</option>@endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="When" name="form.scheduled_at" required>
                    <x-ui.input type="datetime-local" wire:model="form.scheduled_at" />
                </x-ui.field>
                <x-ui.field label="Duration (minutes)" name="form.duration_minutes" required>
                    <x-ui.input type="number" wire:model="form.duration_minutes" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.field label="Location" name="form.location">
                        <x-ui.input wire:model="form.location" placeholder="Google Meet link / office" />
                    </x-ui.field>
                </div>
                <div class="sm:col-span-2">
                    <x-ui.field label="Agenda" name="form.agenda">
                        <x-ui.textarea wire:model="form.agenda" rows="3" />
                    </x-ui.field>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Participants">
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                @foreach ($staff as $person)
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm">
                        <input type="checkbox" value="{{ $person->id }}" wire:model="form.participant_ids"
                               class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                        <span class="truncate">{{ $person->name }}</span>
                    </label>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card title="Action items">
            <div class="space-y-2">
                @foreach ($form->action_items as $i => $item)
                    <div class="grid grid-cols-1 gap-2 rounded-lg border border-slate-200 p-2 sm:grid-cols-[1fr_10rem_9rem_2rem] sm:items-center sm:border-0 sm:p-0" wire:key="ai-{{ $i }}">
                        <input type="text" wire:model="form.action_items.{{ $i }}.description" placeholder="Action"
                               class="rounded-lg border-0 py-1.5 px-2.5 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
                        <select wire:model="form.action_items.{{ $i }}.owner_id"
                                class="rounded-lg border-0 py-1.5 pl-2.5 pr-8 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
                            <option value="">Owner…</option>
                            @foreach ($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}</option>@endforeach
                        </select>
                        <input type="date" wire:model="form.action_items.{{ $i }}.due_date"
                               class="rounded-lg border-0 py-1.5 px-2.5 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
                        <button type="button" wire:click="removeActionItem({{ $i }})" class="justify-self-end text-slate-300 hover:text-red-500">✕</button>
                    </div>
                @endforeach
                <button type="button" wire:click="addActionItem"
                        class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-brand-600 hover:bg-brand-50">＋ Add action item</button>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" :href="$meeting ? route('meetings.show', $meeting) : route('meetings.index')">Cancel</x-ui.button>
                    <x-ui.button type="submit">{{ $meeting ? 'Save changes' : 'Schedule meeting' }}</x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
