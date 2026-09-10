<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Bug;
use App\Models\User;

class BugPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function view(User $user, Bug $bug): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageBugs->value);
    }

    public function update(User $user, Bug $bug): bool
    {
        return $user->can(Permission::ManageBugs->value)
            || $bug->assigned_to === $user->id;
    }

    public function delete(User $user, Bug $bug): bool
    {
        return $user->can(Permission::ManageBugs->value)
            && $bug->project->lead_id === $user->id;
    }
}
