<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewCustomers->value);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->can(Permission::ViewCustomers->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CreateCustomers->value);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->can(Permission::EditCustomers->value);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->can(Permission::DeleteCustomers->value);
    }

    public function restore(User $user, Customer $customer): bool
    {
        return $user->can(Permission::DeleteCustomers->value);
    }
}
