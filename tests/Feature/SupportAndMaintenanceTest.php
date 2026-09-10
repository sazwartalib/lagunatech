<?php

use App\Actions\Support\AddTicketReply;
use App\Enums\BillingCycle;
use App\Enums\Role;
use App\Enums\SupportTicketStatus;
use App\Livewire\Maintenance\MaintenanceForm;
use App\Livewire\Support\TicketForm;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\MaintenancePlan;
use App\Models\SupportTicket;
use App\Notifications\SupportTicketOpened;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('staff can open a ticket and support staff are notified', function () {
    Notification::fake();
    actingAsRole(Role::Support);
    $supportPerson = userWithRole(Role::Support);
    $customer = Customer::factory()->create();

    Livewire::test(TicketForm::class)
        ->set('customer_id', $customer->id)
        ->set('subject', 'Cannot access the reports page')
        ->set('description', 'Getting a 500 error since this morning.')
        ->set('priority', 'high')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $ticket = SupportTicket::firstWhere('subject', 'Cannot access the reports page');

    expect($ticket->reference)->toMatch('/^TKT-\d{4}-\d{3}$/');
    Notification::assertSentTo($supportPerson, SupportTicketOpened::class);
});

test('a staff reply moves an open ticket to waiting-customer', function () {
    $staff = userWithRole(Role::Support);
    $ticket = SupportTicket::factory()->status(SupportTicketStatus::Open)->create();

    app(AddTicketReply::class)->handle($ticket, 'We are looking into it.', $staff);

    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::WaitingCustomer);
});

test('a customer reply re-opens a waiting ticket', function () {
    $ticket = SupportTicket::factory()->status(SupportTicketStatus::WaitingCustomer)->create();
    $customerUser = CustomerUser::factory()->create(['customer_id' => $ticket->customer_id]);

    app(AddTicketReply::class)->handle($ticket, 'Still broken.', $customerUser);

    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::InProgress);
});

test('internal notes never change the ticket status', function () {
    $staff = userWithRole(Role::Support);
    $ticket = SupportTicket::factory()->status(SupportTicketStatus::Open)->create();

    app(AddTicketReply::class)->handle($ticket, 'Suspect a caching bug.', $staff, internal: true);

    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::Open)
        ->and($ticket->replies()->first()->is_internal)->toBeTrue();
});

test('only a super admin may delete a ticket', function () {
    $support = userWithRole(Role::Support);
    $superAdmin = userWithRole(Role::SuperAdmin);
    $ticket = SupportTicket::factory()->create();

    expect($support->can('delete', $ticket))->toBeFalse()
        ->and($superAdmin->can('delete', $ticket))->toBeTrue();
});

test('a maintenance plan computes its next renewal date and annual value', function () {
    actingAsRole(Role::Finance);
    $customer = Customer::factory()->create();

    Livewire::test(MaintenanceForm::class)
        ->set('customer_id', $customer->id)
        ->set('name', 'Standard Support')
        ->set('billing_cycle', BillingCycle::Monthly->value)
        ->set('fee', '500')
        ->set('starts_on', now()->subMonths(2)->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    $plan = MaintenancePlan::firstWhere('name', 'Standard Support');

    expect($plan->next_renewal_on)->not->toBeNull()
        ->and($plan->next_renewal_on->isFuture())->toBeTrue()
        ->and($plan->annual_value)->toBe(6000.0);
});

test('an ad-hoc maintenance plan has no renewal date', function () {
    actingAsRole(Role::Finance);
    $customer = Customer::factory()->create();

    Livewire::test(MaintenanceForm::class)
        ->set('customer_id', $customer->id)
        ->set('name', 'Ad-hoc')
        ->set('billing_cycle', BillingCycle::AdHoc->value)
        ->set('fee', '0')
        ->set('starts_on', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    expect(MaintenancePlan::firstWhere('name', 'Ad-hoc')->next_renewal_on)->toBeNull();
});
