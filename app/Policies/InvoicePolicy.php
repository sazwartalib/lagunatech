<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny([Permission::ManageInvoices->value, Permission::ViewFinancialReports->value]);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ManageInvoices->value);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can(Permission::ManageInvoices->value) && $invoice->status->isEditable();
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can(Permission::ManageInvoices->value) && $invoice->payments()->doesntExist();
    }

    public function recordPayment(User $user, Invoice $invoice): bool
    {
        return $user->can(Permission::RecordPayments->value) && $invoice->status->isOpen();
    }
}
