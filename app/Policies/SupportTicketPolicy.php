<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ManageSupport->value);
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        return $user->can(Permission::ManageSupport->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageSupport->value);
    }

    public function update(User $user, SupportTicket $ticket): bool
    {
        return $user->can(Permission::ManageSupport->value);
    }

    public function reply(User $user, SupportTicket $ticket): bool
    {
        return $user->can(Permission::ManageSupport->value);
    }

    public function delete(User $user, SupportTicket $ticket): bool
    {
        return $user->hasRole(Role::SuperAdmin->value);
    }
}
