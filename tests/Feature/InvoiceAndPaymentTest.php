<?php

use App\Actions\Invoices\CreateInvoiceFromQuotation;
use App\Actions\Payments\RecordPayment;
use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Livewire\Invoices\InvoiceForm;
use App\Livewire\Invoices\InvoiceShow;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Quotation;
use Livewire\Livewire;

test('an invoice is created with computed totals', function () {
    actingAsRole(Role::Finance);
    $customer = Customer::factory()->create();

    Livewire::test(InvoiceForm::class)
        ->set('form.customer_id', $customer->id)
        ->set('form.title', 'Deposit')
        ->set('form.items', [
            ['description' => 'Deposit 50%', 'quantity' => 1, 'unit_price' => 6000],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $invoice = Invoice::firstWhere('title', 'Deposit');

    expect($invoice->reference)->toMatch('/^INV-\d{4}-\d{3}$/')
        ->and((float) $invoice->total)->toBe(6000.0)
        ->and($invoice->outstanding)->toBe(6000.0);
});

test('an invoice can be spun up from a quotation', function () {
    actingAsRole(Role::Finance);
    $quotation = Quotation::factory()->approved()->create();

    $invoice = app(CreateInvoiceFromQuotation::class)->handle($quotation->load('items'));

    expect($invoice->quotation_id)->toBe($quotation->id)
        ->and($invoice->customer_id)->toBe($quotation->customer_id)
        ->and((float) $invoice->total)->toBe((float) $quotation->total)
        ->and($invoice->items)->toHaveCount($quotation->items->count())
        ->and($invoice->status)->toBe(InvoiceStatus::Draft);
});

test('recording a full payment settles the invoice', function () {
    actingAsRole(Role::Finance);
    $invoice = Invoice::factory()->status(InvoiceStatus::Sent)->create();

    app(RecordPayment::class)->handle($invoice, [
        'amount' => $invoice->total,
        'paid_on' => now()->toDateString(),
        'method' => 'bank_transfer',
    ]);

    $invoice->refresh();

    expect($invoice->status)->toBe(InvoiceStatus::Paid)
        ->and((float) $invoice->amount_paid)->toBe((float) $invoice->total)
        ->and($invoice->outstanding)->toBe(0.0)
        ->and($invoice->paid_at)->not->toBeNull();
});

test('a partial payment moves the invoice to partially paid and tracks the balance', function () {
    actingAsRole(Role::Finance);
    $invoice = Invoice::factory()->status(InvoiceStatus::Sent)->create(['due_date' => now()->addWeeks(2)]);
    $half = round((float) $invoice->total / 2, 2);

    app(RecordPayment::class)->handle($invoice, [
        'amount' => $half,
        'paid_on' => now()->toDateString(),
        'method' => 'cash',
    ]);

    $invoice->refresh();

    expect($invoice->status)->toBe(InvoiceStatus::PartiallyPaid)
        ->and($invoice->outstanding)->toBe(round((float) $invoice->total - $half, 2));
});

test('an unpaid invoice past its due date is detected as overdue', function () {
    $invoice = Invoice::factory()->overdue()->create();

    expect($invoice->is_overdue)->toBeTrue()
        ->and(Invoice::query()->overdue()->pluck('id'))->toContain($invoice->id);
});

test('a developer cannot reach an invoice or record a payment', function () {
    $this->actingAs(userWithRole(Role::Developer));
    $invoice = Invoice::factory()->status(InvoiceStatus::Sent)->create();

    Livewire::test(InvoiceShow::class, ['invoice' => $invoice])->assertForbidden();
});

test('a project manager can view an invoice but not record a payment', function () {
    // PM has "manage invoices" (so can view) but not "record payments".
    $this->actingAs(userWithRole(Role::ProjectManager));
    $invoice = Invoice::factory()->status(InvoiceStatus::Sent)->create();

    Livewire::test(InvoiceShow::class, ['invoice' => $invoice])
        ->assertOk()
        ->set('payAmount', '100')
        ->set('payDate', now()->toDateString())
        ->call('recordPayment')
        ->assertForbidden();
});

test('payments cannot be deleted by finance', function () {
    $finance = userWithRole(Role::Finance);
    $payment = Payment::factory()->create();

    expect($finance->can('delete', $payment))->toBeFalse();
});

test('a fully paid invoice cannot be edited', function () {
    $finance = userWithRole(Role::Finance);
    $invoice = Invoice::factory()->status(InvoiceStatus::Paid)->create();

    expect($finance->can('update', $invoice))->toBeFalse();
});
