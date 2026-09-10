<?php

namespace App\Livewire\Forms;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class CustomerForm extends Form
{
    public ?int $customerId = null;

    public string $company_name = '';

    public string $contact_person = '';

    public string $email = '';

    public string $phone = '';

    public string $registration_number = '';

    public string $address = '';

    public string $status = 'prospect';

    public ?int $account_manager_id = null;

    public string $notes = '';

    public function setCustomer(Customer $customer): void
    {
        $this->customerId = $customer->id;
        $this->company_name = $customer->company_name;
        $this->contact_person = (string) $customer->contact_person;
        $this->email = (string) $customer->email;
        $this->phone = (string) $customer->phone;
        $this->registration_number = (string) $customer->registration_number;
        $this->address = (string) $customer->address;
        $this->status = $customer->status->value;
        $this->account_manager_id = $customer->account_manager_id;
        $this->notes = (string) $customer->notes;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', new Enum(CustomerStatus::class)],
            'account_manager_id' => ['nullable', Rule::exists('users', 'id')],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = $this->validate();
        unset($data['customerId']);

        return $data;
    }
}
