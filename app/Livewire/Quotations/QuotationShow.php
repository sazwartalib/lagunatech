<?php

namespace App\Livewire\Quotations;

use App\Actions\Invoices\CreateInvoiceFromQuotation;
use App\Actions\Quotations\ConvertQuotationToProject;
use App\Actions\Quotations\TransitionQuotationStatus;
use App\Enums\QuotationStatus;
use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class QuotationShow extends Component
{
    public Quotation $quotation;

    public function mount(Quotation $quotation): void
    {
        $this->authorize('view', $quotation);
        $this->quotation = $quotation;
    }

    public function changeStatus(string $status, TransitionQuotationStatus $action): void
    {
        $target = QuotationStatus::from($status);

        $ability = in_array($target, [QuotationStatus::Approved, QuotationStatus::Rejected], true) ? 'decide' : 'update';
        $this->authorize($ability, $this->quotation);

        $action->handle($this->quotation, $target);
        $this->quotation->refresh();

        $this->dispatch('toast', message: "Quotation marked as {$target->label()}.");
    }

    public function convertToProject(ConvertQuotationToProject $action): void
    {
        $this->authorize('convert', $this->quotation);

        $project = $action->handle($this->quotation);

        session()->flash('status', "Project {$project->reference} created from this quotation.");
        $this->redirectRoute('projects.show', $project, navigate: true);
    }

    public function createInvoice(CreateInvoiceFromQuotation $action): void
    {
        $this->authorize('create', Invoice::class);

        $invoice = $action->handle($this->quotation->loadMissing('items'));

        session()->flash('status', "Draft invoice {$invoice->reference} created.");
        $this->redirectRoute('invoices.edit', $invoice, navigate: true);
    }

    public function render(): View
    {
        $this->quotation->loadMissing(['customer', 'items', 'creator:id,name', 'project:id,name,reference']);

        return view('livewire.quotations.quotation-show', [
            'title' => $this->quotation->reference,
        ]);
    }
}
