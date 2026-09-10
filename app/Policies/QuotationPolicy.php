<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny([Permission::ManageQuotations->value, Permission::ViewFinancialReports->value]);
    }

    public function view(User $user, Quotation $quotation): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageQuotations->value);
    }

    public function update(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::ManageQuotations->value) && $quotation->status->isEditable();
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::ManageQuotations->value) && ! $quotation->status->isDecided();
    }

    public function decide(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::ApproveQuotations->value);
    }

    public function convert(User $user, Quotation $quotation): bool
    {
        return $user->can(Permission::CreateProjects->value);
    }
}
