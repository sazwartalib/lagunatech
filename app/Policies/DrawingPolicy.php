<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Drawing;
use App\Models\User;

class DrawingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewDrawings->value);
    }

    public function view(User $user, Drawing $drawing): bool
    {
        return $user->can(Permission::ViewDrawings->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CreateDrawings->value);
    }

    public function update(User $user, Drawing $drawing): bool
    {
        return $user->can(Permission::EditDrawings->value);
    }

    public function delete(User $user, Drawing $drawing): bool
    {
        return $user->can(Permission::DeleteDrawings->value);
    }

    public function export(User $user, Drawing $drawing): bool
    {
        return $user->can(Permission::ExportDrawings->value);
    }
}
