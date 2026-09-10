<?php

namespace App\Policies;

use App\Enums\ChangeRequestStatus;
use App\Enums\Permission;
use App\Models\ChangeRequest;
use App\Models\User;

class ChangeRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function view(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can(Permission::ViewProjects->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageChangeRequests->value);
    }

    public function update(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can(Permission::ManageChangeRequests->value) && ! $changeRequest->status->isClosed();
    }

    public function delete(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can(Permission::ManageChangeRequests->value)
            && $changeRequest->status === ChangeRequestStatus::Pending;
    }

    /**
     * Approving a change request commits the customer to extra cost/time.
     */
    public function decide(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can(Permission::ApproveQuotations->value)
            || $user->can(Permission::ManageChangeRequests->value);
    }

    public function convert(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can(Permission::ManageTasks->value)
            && $changeRequest->status === ChangeRequestStatus::Approved
            && $changeRequest->task_id === null;
    }
}
