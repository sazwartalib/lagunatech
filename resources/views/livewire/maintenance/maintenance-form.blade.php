<div class="mx-auto max-w-2xl">
    <x-ui.page-header :title="$plan ? 'Edit maintenance plan' : 'New maintenance plan'">
        <x-slot:actions>
            <x-ui.button variant="secondary" href="{{ route('maintenance.index') }}">Cancel</x-ui.button>
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
                    <x-ui.field label="Plan name" name="name" required>
                        <x-ui.input wire:model="name" placeholder="Standard Support" />
                    </x-ui.field>
                </div>
                <x-ui.field label="Billing cycle" name="billing_cycle" required>
                    <x-ui.select wire:model="billing_cycle" :options="App\Enums\BillingCycle::options()" />
                </x-ui.field>
                <x-ui.field label="Fee per cycle (RM)" name="fee" required>
                    <x-ui.input type="number" step="0.01" wire:model="fee" />
                </x-ui.field>
                <x-ui.field label="Starts on" name="starts_on" required>
                    <x-ui.input type="date" wire:model="starts_on" />
                </x-ui.field>
                <x-ui.field label="Ends on (optional)" name="ends_on">
                    <x-ui.input type="date" wire:model="ends_on" />
                </x-ui.field>
                <x-ui.field label="Status" name="status" required>
                    <x-ui.select wire:model="status" :options="App\Enums\MaintenanceStatus::options()" />
                </x-ui.field>
                <div class="sm:col-span-2">
                    <x-ui.field label="Description" name="description">
                        <x-ui.textarea wire:model="description" rows="3" />
                    </x-ui.field>
                </div>
            </div>
            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" href="{{ route('maintenance.index') }}">Cancel</x-ui.button>
                    <x-ui.button type="submit">{{ $plan ? 'Save changes' : 'Create plan' }}</x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
