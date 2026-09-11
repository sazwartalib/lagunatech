<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Lead;
use App\Models\User;

/**
 * Leads sit in front of customers in the pipeline, so they reuse the customer
 * capabilities: anyone who can view customers can view leads; anyone who can
 * create customers can work and convert them.
 */
class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewCustomers->value);
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can(Permission::ViewCustomers->value);
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can(Permission::CreateCustomers->value);
    }

    public function convert(User $user, Lead $lead): bool
    {
        return $user->can(Permission::CreateCustomers->value)
            && $lead->converted_customer_id === null;
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can(Permission::CreateCustomers->value);
    }
}
