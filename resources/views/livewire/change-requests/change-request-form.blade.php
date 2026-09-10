<div class="mx-auto max-w-2xl">
    <x-ui.page-header :title="$changeRequest ? 'Edit change request' : 'New change request'" :subtitle="$changeRequest?->reference">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="$changeRequest ? route('change-requests.show', $changeRequest) : route('change-requests.index')">Cancel</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save">
        <x-ui.card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-ui.field label="Project" name="form.project_id" required>
                        <x-ui.select wire:model="form.project_id" placeholder="Select project">
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->reference }} · {{ $project->name }} ({{ $project->customer->company_name }})</option>
                            @endforeach
                        </x-ui.select>
                    </x-ui.field>
                </div>

                <div class="sm:col-span-2">
                    <x-ui.field label="Request" name="form.title" required>
                        <x-ui.input wire:model="form.title" placeholder="e.g. Add WhatsApp notifications" />
                    </x-ui.field>
                </div>

                <div class="sm:col-span-2">
                    <x-ui.field label="Description" name="form.description">
                        <x-ui.textarea wire:model="form.description" rows="4" placeholder="What is the customer asking for, and why?" />
                    </x-ui.field>
                </div>

                <x-ui.field label="Estimated cost (RM)" name="form.estimated_cost">
                    <x-ui.input type="number" step="0.01" wire:model="form.estimated_cost" />
                </x-ui.field>
                <x-ui.field label="Additional days" name="form.additional_days">
                    <x-ui.input type="number" wire:model="form.additional_days" />
                </x-ui.field>

                <x-ui.field label="Assign to" name="form.assigned_to">
                    <x-ui.select wire:model="form.assigned_to" placeholder="Unassigned">
                        @foreach ($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}</option>@endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Status" name="form.status" required>
                    <x-ui.select wire:model="form.status" :options="App\Enums\ChangeRequestStatus::options()" />
                </x-ui.field>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" :href="$changeRequest ? route('change-requests.show', $changeRequest) : route('change-requests.index')">Cancel</x-ui.button>
                    <x-ui.button type="submit">{{ $changeRequest ? 'Save changes' : 'Create change request' }}</x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
