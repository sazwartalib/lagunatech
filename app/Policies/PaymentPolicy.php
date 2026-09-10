<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny([Permission::RecordPayments->value, Permission::ViewFinancialReports->value]);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::RecordPayments->value);
    }

    /**
     * Deleting financial records is deliberately restricted to super admins
     * (handled by the Gate::before check) — nobody else, even Finance.
     */
    public function delete(User $user, Payment $payment): bool
    {
        return false;
    }
}
