<?php

namespace App\Livewire\Customers;

use App\Actions\Customers\UpsertCustomer;
use App\Livewire\Forms\CustomerForm as CustomerFormData;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CustomerForm extends Component
{
    public CustomerFormData $form;

    public ?Customer $customer = null;

    public function mount(?Customer $customer = null): void
    {
        if ($customer?->exists) {
            $this->authorize('update', $customer);
            $this->customer = $customer;
            $this->form->setCustomer($customer);
        } else {
            $this->authorize('create', Customer::class);
        }
    }

    public function save(UpsertCustomer $upsert): void
    {
        $customer = $upsert->handle($this->form->validatedData(), $this->customer);

        session()->flash('status', $this->customer ? 'Customer updated.' : 'Customer created.');

        $this->redirectRoute('customers.show', $customer, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.customers.customer-form', [
            'title' => $this->customer ? 'Edit '.$this->customer->company_name : 'New customer',
            'managers' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
