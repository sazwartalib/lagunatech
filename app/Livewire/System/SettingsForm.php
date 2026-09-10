<?php

namespace App\Livewire\System;

use App\Enums\Permission;
use App\Support\Settings;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Settings')]
class SettingsForm extends Component
{
    public string $companyName = '';

    public string $companyRegistrationNo = '';

    public string $companyAddress = '';

    public string $companyEmail = '';

    public string $companyPhone = '';

    public string $companyBankDetails = '';

    public float $defaultTaxRate = 0;

    public int $quotationValidityDays = 30;

    public int $invoiceDueDays = 30;

    public string $currency = 'RM';

    public string $paymentTerms = '';

    public function mount(Settings $settings): void
    {
        abort_unless(auth()->user()->can(Permission::ManageSystemSettings->value), 403);

        $this->companyName = (string) $settings->get('company.name');
        $this->companyRegistrationNo = (string) $settings->get('company.registration_no');
        $this->companyAddress = (string) $settings->get('company.address');
        $this->companyEmail = (string) $settings->get('company.email');
        $this->companyPhone = (string) $settings->get('company.phone');
        $this->companyBankDetails = (string) $settings->get('company.bank_details');
        $this->defaultTaxRate = (float) $settings->get('finance.default_tax_rate');
        $this->quotationValidityDays = (int) $settings->get('finance.quotation_validity_days');
        $this->invoiceDueDays = (int) $settings->get('finance.invoice_due_days');
        $this->currency = (string) $settings->get('finance.currency');
        $this->paymentTerms = (string) $settings->get('finance.payment_terms');
    }

    public function save(Settings $settings): void
    {
        abort_unless(auth()->user()->can(Permission::ManageSystemSettings->value), 403);

        $validated = $this->validate([
            'companyName' => ['required', 'string', 'max:255'],
            'companyRegistrationNo' => ['nullable', 'string', 'max:100'],
            'companyAddress' => ['nullable', 'string', 'max:1000'],
            'companyEmail' => ['nullable', 'email', 'max:255'],
            'companyPhone' => ['nullable', 'string', 'max:50'],
            'companyBankDetails' => ['nullable', 'string', 'max:1000'],
            'defaultTaxRate' => ['numeric', 'min:0', 'max:100'],
            'quotationValidityDays' => ['integer', 'min:1', 'max:365'],
            'invoiceDueDays' => ['integer', 'min:1', 'max:365'],
            'currency' => ['required', 'string', 'max:5'],
            'paymentTerms' => ['nullable', 'string', 'max:5000'],
        ]);

        $settings->setMany([
            'company.name' => $validated['companyName'],
            'company.registration_no' => $validated['companyRegistrationNo'],
            'company.address' => $validated['companyAddress'],
            'company.email' => $validated['companyEmail'],
            'company.phone' => $validated['companyPhone'],
            'company.bank_details' => $validated['companyBankDetails'],
            'finance.default_tax_rate' => $validated['defaultTaxRate'],
            'finance.quotation_validity_days' => $validated['quotationValidityDays'],
            'finance.invoice_due_days' => $validated['invoiceDueDays'],
            'finance.currency' => $validated['currency'],
            'finance.payment_terms' => $validated['paymentTerms'],
        ]);

        $this->dispatch('toast', message: 'Settings saved.');
    }

    public function render(): View
    {
        return view('livewire.system.settings-form');
    }
}
