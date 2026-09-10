<div class="mx-auto max-w-2xl">
    <x-ui.page-header :title="$staff ? 'Edit staff member' : 'Add staff member'">
        <x-slot:actions>
            <x-ui.button variant="secondary" href="{{ route('staff.index') }}">Cancel</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form wire:submit="save">
        <x-ui.card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ui.field label="Full name" name="name" required>
                    <x-ui.input wire:model="name" />
                </x-ui.field>
                <x-ui.field label="Email" name="email" required>
                    <x-ui.input type="email" wire:model="email" />
                </x-ui.field>
                <x-ui.field label="Phone" name="phone">
                    <x-ui.input wire:model="phone" />
                </x-ui.field>
                <x-ui.field label="Position" name="position">
                    <x-ui.input wire:model="position" placeholder="Software Engineer" />
                </x-ui.field>
                <x-ui.field label="Department" name="department">
                    <x-ui.input wire:model="department" placeholder="Engineering" />
                </x-ui.field>
                <x-ui.field label="Account status" name="is_active">
                    <label class="mt-1.5 flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                        Active — can sign in
                    </label>
                </x-ui.field>

                <div class="sm:col-span-2">
                    <x-ui.field label="Roles" name="roles" required hint="Determines what this person can see and do.">
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($allRoles as $role)
                                <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm">
                                    <input type="checkbox" value="{{ $role->value }}" wire:model="roles"
                                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                                    <span class="truncate">{{ $role->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </x-ui.field>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-ui.button variant="secondary" href="{{ route('staff.index') }}">Cancel</x-ui.button>
                    <x-ui.button type="submit">{{ $staff ? 'Save changes' : 'Add staff member' }}</x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.card>
    </form>
</div>
