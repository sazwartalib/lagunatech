<?php

namespace App\Livewire\Invoices;

use App\Actions\Invoices\SyncInvoiceState;
use App\Actions\Payments\RecordPayment;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class InvoiceShow extends Component
{
    public Invoice $invoice;

    public bool $showPaymentModal = false;

    #[Validate('required|numeric|min:0.01')]
    public string $payAmount = '';

    #[Validate('required|date')]
    public string $payDate = '';

    #[Validate('required')]
    public string $payMethod = 'bank_transfer';

    #[Validate('nullable|string|max:100')]
    public string $payReferenceNumber = '';

    #[Validate('nullable|string|max:1000')]
    public string $payNotes = '';

    public function mount(Invoice $invoice): void
    {
        $this->authorize('view', $invoice);
        $this->invoice = $invoice;
        $this->payDate = now()->toDateString();
        $this->payAmount = (string) $invoice->outstanding;
    }

    public function markSent(SyncInvoiceState $sync): void
    {
        $this->authorize('update', $this->invoice);
        abort_unless($this->invoice->status === InvoiceStatus::Draft, 403);

        $this->invoice->update(['status' => InvoiceStatus::Sent, 'sent_at' => now()]);
        $sync->handle($this->invoice);
        $this->invoice->refresh();

        $this->dispatch('toast', message: 'Invoice marked as sent.');
    }

    public function cancel(): void
    {
        $this->authorize('update', $this->invoice);
        $this->invoice->update(['status' => InvoiceStatus::Cancelled]);
        $this->invoice->refresh();
        $this->dispatch('toast', message: 'Invoice cancelled.', type: 'info');
    }

    public function recordPayment(RecordPayment $action): void
    {
        $this->authorize('recordPayment', $this->invoice);
        $this->validate();

        $action->handle($this->invoice, [
            'amount' => $this->payAmount,
            'paid_on' => $this->payDate,
            'method' => $this->payMethod,
            'reference_number' => $this->payReferenceNumber ?: null,
            'notes' => $this->payNotes ?: null,
        ]);

        $this->invoice->refresh();
        $this->reset('showPaymentModal', 'payReferenceNumber', 'payNotes');
        $this->payAmount = (string) $this->invoice->outstanding;
        $this->dispatch('toast', message: 'Payment recorded.');
    }

    public function render(): View
    {
        $this->invoice->loadMissing([
            'customer', 'items', 'creator:id,name',
            'project:id,name,reference', 'quotation:id,reference',
            'payments.recorder:id,name',
        ]);

        return view('livewire.invoices.invoice-show', [
            'title' => $this->invoice->reference,
            'methods' => PaymentMethod::options(),
        ]);
    }
}
