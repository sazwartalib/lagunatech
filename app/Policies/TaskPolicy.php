<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function view(User $user, Task $task): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageTasks->value);
    }

    public function update(User $user, Task $task): bool
    {
        return $user->can(Permission::ManageTasks->value)
            || $task->assignee_id === $user->id
            || $task->project->lead_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->can(Permission::ManageTasks->value)
            || $task->project->lead_id === $user->id;
    }
}
