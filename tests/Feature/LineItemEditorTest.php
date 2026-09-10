<?php

use App\Enums\Role;
use App\Livewire\Invoices\InvoiceForm;
use App\Livewire\Meetings\MeetingForm;
use App\Livewire\Quotations\QuotationForm;
use Livewire\Livewire;

/**
 * Livewire cannot invoke methods on nested Form objects from the frontend, so
 * every repeatable-row editor exposes add/remove proxies on its component.
 */
test('quotation line items can be added and removed', function () {
    actingAsRole(Role::Finance);

    Livewire::test(QuotationForm::class)
        ->assertCount('form.items', 1)
        ->call('addLine')
        ->call('addLine')
        ->assertCount('form.items', 3)
        ->call('removeLine', 1)
        ->assertCount('form.items', 2);
});

test('removing the last quotation line keeps one empty row', function () {
    actingAsRole(Role::Finance);

    Livewire::test(QuotationForm::class)
        ->call('removeLine', 0)
        ->assertCount('form.items', 1);
});

test('invoice line items can be added and removed', function () {
    actingAsRole(Role::Finance);

    Livewire::test(InvoiceForm::class)
        ->call('addLine')
        ->assertCount('form.items', 2)
        ->call('removeLine', 0)
        ->assertCount('form.items', 1);
});

test('meeting action items can be added and removed', function () {
    actingAsRole(Role::ProjectManager);

    Livewire::test(MeetingForm::class)
        ->assertCount('form.action_items', 0)
        ->call('addActionItem')
        ->call('addActionItem')
        ->assertCount('form.action_items', 2)
        ->call('removeActionItem', 0)
        ->assertCount('form.action_items', 1);
});
