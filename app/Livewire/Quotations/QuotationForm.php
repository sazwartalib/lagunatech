<?php

namespace App\Livewire\Quotations;

use App\Actions\Quotations\UpsertQuotation;
use App\Livewire\Forms\QuotationForm as QuotationFormData;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class QuotationForm extends Component
{
    public QuotationFormData $form;

    public ?Quotation $quotation = null;

    public function mount(?Quotation $quotation = null): void
    {
        if ($quotation?->exists) {
            $this->authorize('update', $quotation);
            $this->quotation = $quotation;
            $quotation->loadMissing('items');
            $this->form->setQuotation($quotation);

            return;
        }

        $this->authorize('create', Quotation::class);
        $this->form->mountDefaults();
        $this->form->customer_id = request()->integer('customer') ?: null;
        $this->form->project_id = request()->integer('project') ?: null;

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

    public function save(UpsertQuotation $upsert): void
    {
        ['data' => $data, 'items' => $items] = $this->form->validatedData();

        $quotation = $upsert->handle($data, $items, $this->quotation);

        session()->flash('status', $this->quotation ? 'Quotation updated.' : "Quotation {$quotation->reference} created.");

        $this->redirectRoute('quotations.show', $quotation, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.quotations.quotation-form', [
            'title' => $this->quotation ? 'Edit '.$this->quotation->reference : 'New quotation',
            'customers' => Customer::query()->orderBy('company_name')->get(['id', 'company_name']),
            'preview' => $this->form->livePreview(),
        ]);
    }
}
