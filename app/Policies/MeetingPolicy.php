<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function view(User $user, Meeting $meeting): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageMeetings->value);
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return $user->can(Permission::ManageMeetings->value);
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return $user->can(Permission::ManageMeetings->value)
            && $meeting->created_by === $user->id;
    }
}
