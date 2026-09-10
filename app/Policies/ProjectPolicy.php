<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function view(User $user, Project $project): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CreateProjects->value);
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->can(Permission::EditProjects->value)) {
            return true;
        }

        // The internal PIC can always keep their own project moving.
        return $project->lead_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->can(Permission::DeleteProjects->value);
    }

    public function manageTasks(User $user, Project $project): bool
    {
        return $user->can(Permission::ManageTasks->value)
            || $project->lead_id === $user->id
            || $project->members->contains($user);
    }
}
