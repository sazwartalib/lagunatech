<div class="mx-auto max-w-2xl">
    <x-ui.page-header :title="$bug ? 'Edit bug' : 'Log a bug'" :subtitle="$bug?->reference">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="$bug ? route('bugs.show', $bug) : route('bugs.index')">Cancel</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save">
        <x-ui.card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-ui.field label="Project" name="form.project_id" required>
                        <x-ui.select wire:model="form.project_id" placeholder="Select project">
                            @foreach ($projects as $project)<option value="{{ $project->id }}">{{ $project->reference }} · {{ $project->name }}</option>@endforeach
                        </x-ui.select>
                    </x-ui.field>
                </div>
                <div class="sm:col-span-2">
                    <x-ui.field label="Title" name="form.title" required>
                        <x-ui.input wire:model="form.title" placeholder="Short summary of the problem" />
                    </x-ui.field>
                </div>
                <div class="sm:col-span-2">
                    <x-ui.field label="Description" name="form.description" hint="Steps to reproduce, expected vs actual.">
                        <x-ui.textarea wire:model="form.description" rows="4" />
                    </x-ui.field>
                </div>
                <x-ui.field label="Priority" name="form.priority" required>
                    <x-ui.select wire:model="form.priority" :options="App\Enums\Priority::options()" />
                </x-ui.field>
                <x-ui.field label="Status" name="form.status" required>
                    <x-ui.select wire:model="form.status" :options="App\Enums\BugStatus::options()" />
                </x-ui.field>
                <x-ui.field label="Assign to" name="form.assigned_to">
                    <x-ui.select wire:model="form.assigned_to" placeholder="Unassigned">
                        @foreach ($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}</option>@endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Due date" name="form.due_date">
                    <x-ui.input type="date" wire:model="form.due_date" />
                </x-ui.field>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" :href="$bug ? route('bugs.show', $bug) : route('bugs.index')">Cancel</x-ui.button>
                    <x-ui.button type="submit">{{ $bug ? 'Save changes' : 'Log bug' }}</x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
