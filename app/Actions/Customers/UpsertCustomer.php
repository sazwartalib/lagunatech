<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use App\Support\ReferenceGenerator;

class UpsertCustomer
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    /**
     * Create a new customer or update an existing one.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?Customer $customer = null): Customer
    {
        $customer ??= new Customer;

        if (! $customer->exists) {
            $customer->reference = $this->references->customerReference();
        }

        $customer->fill($data)->save();

        return $customer->refresh();
    }
}
