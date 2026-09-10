<div>
    <x-ui.page-header title="Staff" subtitle="People with access to {{ config('app.name') }}.">
        <x-slot:actions>
            @can('create', App\Models\User::class)
                <x-ui.button href="{{ route('staff.create') }}" icon="＋">Add staff</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name or email…"
                   class="w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-brand-600">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" wire:model.live="activeOnly" class="rounded border-slate-300 text-brand-600 focus:ring-brand-600"> Active only
        </label>
    </div>

    <x-ui.card flush>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Name</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Position</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Roles</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Status</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($staff as $person)
                        <tr wire:key="u-{{ $person->id }}" class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-ui.avatar :name="$person->name" size="sm" />
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $person->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $person->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $person->position ?: '—' }}<span class="block text-xs text-slate-400">{{ $person->department }}</span></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($person->roles as $role)
                                        <x-ui.badge color="slate" size="xs">{{ \Illuminate\Support\Str::of($role->name)->headline() }}</x-ui.badge>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge :color="$person->is_active ? 'green' : 'slate'" size="xs" dot>{{ $person->is_active ? 'Active' : 'Inactive' }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    @can('update', $person)
                                        <a href="{{ route('staff.edit', $person) }}" wire:navigate class="text-sm font-medium text-brand-600 hover:text-brand-700">Edit</a>
                                    @endcan
                                    <button wire:click="toggleActive({{ $person->id }})"
                                            wire:confirm="{{ $person->is_active ? 'Deactivate this account?' : 'Reactivate this account?' }}"
                                            class="text-sm font-medium text-slate-400 hover:text-slate-700">
                                        {{ $person->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10"><x-ui.empty-state icon="👥" title="No staff found" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($staff->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $staff->links() }}</div>
        @endif
    </x-ui.card>
</div>
