@php use Illuminate\Support\Str; @endphp

<div>
    <x-ui.page-header title="Roles & Permissions" subtitle="What each role can do. Super Admin always has everything.">
        <x-slot:actions>
            <x-ui.button wire:click="save">Save changes</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="sticky top-0 bg-slate-50/90 backdrop-blur">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-500">Permission</th>
                        @foreach ($roles as $role)
                            <th class="px-3 py-3 text-center font-semibold text-slate-600">
                                <span class="block whitespace-nowrap">{{ $role->label() }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($groups as $groupName => $permissions)
                        <tr class="bg-slate-50/60">
                            <td colspan="{{ count($roles) + 1 }}" class="px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $groupName }}</td>
                        </tr>
                        @foreach ($permissions as $permission)
                            <tr wire:key="perm-{{ $permission->value }}" class="hover:bg-slate-50">
                                <td class="px-4 py-2 text-slate-700">{{ Str::of($permission->value)->headline() }}</td>
                                @foreach ($roles as $role)
                                    <td class="px-3 py-2 text-center">
                                        <input type="checkbox"
                                               wire:model="grid.{{ $role->value }}.{{ $permission->value }}"
                                               @disabled($role->value === $superAdmin)
                                               class="rounded border-slate-300 text-brand-600 focus:ring-brand-600 disabled:opacity-40">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-slot:footer>
            <div class="flex justify-end">
                <x-ui.button wire:click="save">Save changes</x-ui.button>
            </div>
        </x-slot:footer>
    </x-ui.card>
</div>
