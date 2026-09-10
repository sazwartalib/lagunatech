<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function view(User $user, Document $document): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageDocuments->value);
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->can(Permission::ManageDocuments->value)
            && ($document->uploaded_by === $user->id || $document->project->lead_id === $user->id
                || $user->can(Permission::EditProjects->value));
    }
}
