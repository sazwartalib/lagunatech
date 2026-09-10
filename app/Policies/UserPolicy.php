<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ManageStaff->value);
    }

    public function view(User $user, User $model): bool
    {
        return $user->can(Permission::ManageStaff->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageStaff->value);
    }

    public function update(User $user, User $model): bool
    {
        return $user->can(Permission::ManageStaff->value);
    }

    /**
     * Nobody may deactivate themselves or the last remaining super admin.
     */
    public function deactivate(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($model->hasRole(Role::SuperAdmin->value)
            && User::role(Role::SuperAdmin->value)->where('is_active', true)->count() <= 1) {
            return false;
        }

        return $user->can(Permission::ManageStaff->value);
    }

    public function delete(User $user, User $model): bool
    {
        return false; // Staff are deactivated, never deleted.
    }
}
