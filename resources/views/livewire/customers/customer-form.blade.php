<div class="mx-auto max-w-3xl">
    <x-ui.page-header :title="$customer ? 'Edit customer' : 'New customer'"
                      :subtitle="$customer?->reference">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="$customer ? route('customers.show', $customer) : route('customers.index')">Cancel</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save">
        <x-ui.card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-ui.field label="Company name" name="form.company_name" required>
                        <x-ui.input wire:model="form.company_name" placeholder="ABC Sdn Bhd" />
                    </x-ui.field>
                </div>

                <x-ui.field label="Contact person" name="form.contact_person">
                    <x-ui.input wire:model="form.contact_person" placeholder="Ahmad" />
                </x-ui.field>

                <x-ui.field label="Status" name="form.status" required>
                    <x-ui.select wire:model="form.status" :options="App\Enums\CustomerStatus::options()" />
                </x-ui.field>

                <x-ui.field label="Email" name="form.email">
                    <x-ui.input type="email" wire:model="form.email" placeholder="ahmad@example.com" />
                </x-ui.field>

                <x-ui.field label="Phone" name="form.phone">
                    <x-ui.input wire:model="form.phone" placeholder="012-3456789" />
                </x-ui.field>

                <x-ui.field label="Company registration no." name="form.registration_number">
                    <x-ui.input wire:model="form.registration_number" placeholder="202601000000 (000000-X)" />
                </x-ui.field>

                <x-ui.field label="Account manager" name="form.account_manager_id">
                    <x-ui.select wire:model="form.account_manager_id" placeholder="Unassigned">
                        @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <div class="sm:col-span-2">
                    <x-ui.field label="Address" name="form.address">
                        <x-ui.textarea wire:model="form.address" rows="2" />
                    </x-ui.field>
                </div>

                <div class="sm:col-span-2">
                    <x-ui.field label="Notes" name="form.notes" hint="Internal — never shown to the customer.">
                        <x-ui.textarea wire:model="form.notes" rows="3" />
                    </x-ui.field>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" :href="$customer ? route('customers.show', $customer) : route('customers.index')">Cancel</x-ui.button>
                    <x-ui.button type="submit">
                        <span wire:loading.remove wire:target="save">{{ $customer ? 'Save changes' : 'Create customer' }}</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
