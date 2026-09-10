<?php

namespace App\Livewire\Invoices;

use App\Actions\Invoices\UpsertInvoice;
use App\Livewire\Forms\InvoiceForm as InvoiceFormData;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class InvoiceForm extends Component
{
    public InvoiceFormData $form;

    public ?Invoice $invoice = null;

    public function mount(?Invoice $invoice = null): void
    {
        if ($invoice?->exists) {
            $this->authorize('update', $invoice);
            $this->invoice = $invoice;
            $invoice->loadMissing('items');
            $this->form->setInvoice($invoice);

            return;
        }

        $this->authorize('create', Invoice::class);
        $this->form->mountDefaults();
        $this->form->project_id = request()->integer('project') ?: null;
        $this->form->customer_id = request()->integer('customer') ?: null;

        if ($this->form->project_id) {
            $this->form->customer_id = Project::find($this->form->project_id)?->customer_id;
        }
    }

    /**
     * Livewire cannot call methods on a nested Form object from the frontend,
     * so the line-item buttons proxy through the component.
     */
    public function addLine(): void
    {
        $this->form->addItem();
    }

    public function removeLine(int $index): void
    {
        $this->form->removeItem($index);
    }

    public function save(UpsertInvoice $upsert): void
    {
        ['data' => $data, 'items' => $items] = $this->form->validatedData();

        $invoice = $upsert->handle($data, $items, $this->invoice);

        session()->flash('status', $this->invoice ? 'Invoice updated.' : "Invoice {$invoice->reference} created.");

        $this->redirectRoute('invoices.show', $invoice, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.invoices.invoice-form', [
            'title' => $this->invoice ? 'Edit '.$this->invoice->reference : 'New invoice',
            'customers' => Customer::query()->orderBy('company_name')->get(['id', 'company_name']),
            'preview' => $this->form->livePreview(),
        ]);
    }
}
