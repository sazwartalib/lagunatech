<div class="mx-auto max-w-2xl">
    <x-ui.page-header title="New support ticket">
        <x-slot:actions>
            <x-ui.button variant="secondary" href="{{ route('support.index') }}">Cancel</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save">
        <x-ui.card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                <div class="sm:col-span-2">
                    <x-ui.field label="Subject" name="subject" required>
                        <x-ui.input wire:model="subject" />
                    </x-ui.field>
                </div>
                <x-ui.field label="Priority" name="priority" required>
                    <x-ui.select wire:model="priority" :options="App\Enums\Priority::options()" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.field label="Description" name="description" required>
                        <x-ui.textarea wire:model="description" rows="4" />
                    </x-ui.field>
                </div>
            </div>
            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" href="{{ route('support.index') }}">Cancel</x-ui.button>
                    <x-ui.button type="submit">Create ticket</x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
