<?php

namespace App\Actions\Leads;

use App\Actions\Customers\UpsertCustomer;
use App\Enums\CustomerStatus;
use App\Enums\LeadStatus;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;

/**
 * Promotes a qualified lead into a customer record, keeping the two linked.
 * Idempotent — a lead already converted returns its existing customer.
 */
class ConvertLeadToCustomer
{
    public function __construct(private readonly UpsertCustomer $upsertCustomer) {}

    public function handle(Lead $lead): Customer
    {
        if ($lead->converted_customer_id !== null) {
            return $lead->convertedCustomer;
        }

        return DB::transaction(function () use ($lead): Customer {
            $customer = $this->upsertCustomer->handle([
                'company_name' => $lead->company ?: $lead->name,
                'contact_person' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'status' => CustomerStatus::Prospect->value,
                'account_manager_id' => $lead->owner_id,
                'notes' => trim(sprintf(
                    "Converted from lead %s.\nProject type: %s\nBudget: %s\n\n%s",
                    $lead->reference,
                    $lead->project_type ?: '—',
                    $lead->budget_range ?: '—',
                    $lead->message ?: '',
                )),
            ]);

            $lead->update([
                'status' => LeadStatus::Won,
                'converted_customer_id' => $customer->id,
                'closed_at' => now(),
            ]);

            return $customer;
        });
    }
}
