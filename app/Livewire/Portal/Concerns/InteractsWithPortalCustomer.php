<?php

namespace App\Livewire\Portal\Concerns;

use App\Models\Customer;
use App\Models\CustomerUser;

trait InteractsWithPortalCustomer
{
    protected function portalUser(): CustomerUser
    {
        return auth('customer')->user();
    }

    protected function customer(): Customer
    {
        return $this->portalUser()->customer;
    }

    /**
     * Guard that a model belongs to the signed-in customer.
     */
    protected function assertOwned(?int $customerId): void
    {
        abort_unless($customerId === $this->customer()->id, 404);
    }
}
