<?php

namespace App\Livewire\System;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Layout('layouts.app')]
#[Title('Roles & Permissions')]
class RoleMatrix extends Component
{
    /**
     * Grid of role name => [permission => bool].
     *
     * @var array<string, array<string, bool>>
     */
    public array $grid = [];

    public function mount(): void
    {
        abort_unless(auth()->user()->can(PermissionEnum::ManageRoles->value), 403);
        $this->loadGrid();
    }

    public function loadGrid(): void
    {
        $roles = Role::with('permissions:id,name')->get();

        foreach (RoleEnum::cases() as $role) {
            $model = $roles->firstWhere('name', $role->value);
            $held = $model?->permissions->pluck('name')->all() ?? [];

            foreach (PermissionEnum::cases() as $permission) {
                $this->grid[$role->value][$permission->value] = in_array($permission->value, $held, true);
            }
        }
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can(PermissionEnum::ManageRoles->value), 403);

        foreach (RoleEnum::cases() as $role) {
            // Super admin always holds everything; the row is locked in the UI.
            if ($role === RoleEnum::SuperAdmin) {
                continue;
            }

            $granted = array_keys(array_filter($this->grid[$role->value] ?? []));
            Role::findByName($role->value, 'web')->syncPermissions($granted);
        }

        App::make(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->dispatch('toast', message: 'Permissions updated.');
    }

    public function render(): View
    {
        $groups = [];
        foreach (PermissionEnum::cases() as $permission) {
            $groups[$permission->group()][] = $permission;
        }

        return view('livewire.system.role-matrix', [
            'roles' => RoleEnum::cases(),
            'groups' => $groups,
            'superAdmin' => RoleEnum::SuperAdmin->value,
        ]);
    }
}
