<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\MaintenancePlan;
use App\Models\User;

class MaintenancePlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ManageMaintenance->value);
    }

    public function view(User $user, MaintenancePlan $plan): bool
    {
        return $user->can(Permission::ManageMaintenance->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageMaintenance->value);
    }

    public function update(User $user, MaintenancePlan $plan): bool
    {
        return $user->can(Permission::ManageMaintenance->value);
    }

    public function delete(User $user, MaintenancePlan $plan): bool
    {
        return $user->can(Permission::ManageMaintenance->value);
    }
}
