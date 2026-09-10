<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Communication;
use App\Models\User;

class CommunicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewCustomers->value);
    }

    public function view(User $user, Communication $communication): bool
    {
        return $user->can(Permission::ViewCustomers->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::LogCommunications->value);
    }

    public function update(User $user, Communication $communication): bool
    {
        return $user->can(Permission::LogCommunications->value)
            && $communication->user_id === $user->id;
    }

    public function delete(User $user, Communication $communication): bool
    {
        return $communication->user_id === $user->id
            && $user->can(Permission::LogCommunications->value);
    }
}
