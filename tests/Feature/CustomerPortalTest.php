<?php

use App\Enums\QuotationStatus;
use App\Livewire\Portal\Dashboard as PortalDashboard;
use App\Livewire\Portal\ProjectShow as PortalProjectShow;
use App\Livewire\Portal\Quotations as PortalQuotations;
use App\Livewire\Portal\Tickets as PortalTickets;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Document;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SupportTicket;
use Livewire\Livewire;

function portalUser(?Customer $customer = null): CustomerUser
{
    $customer ??= Customer::factory()->create();

    return CustomerUser::factory()->create([
        'customer_id' => $customer->id,
        'password' => bcrypt('portal-pass'),
    ]);
}

test('a portal user can sign in and reach their dashboard', function () {
    $user = portalUser();

    $this->post('/portal/login', ['email' => $user->email, 'password' => 'portal-pass'])
        ->assertRedirect('/portal');

    $this->assertAuthenticatedAs($user, 'customer');
});

test('a deactivated portal user cannot sign in', function () {
    $user = portalUser();
    $user->update(['is_active' => false]);

    $this->post('/portal/login', ['email' => $user->email, 'password' => 'portal-pass'])
        ->assertSessionHasErrors('email');

    $this->assertGuest('customer');
});

test('a staff login does not authenticate the customer guard', function () {
    $user = portalUser();

    $this->actingAs($user, 'customer');

    // The default (web) guard is still a guest.
    $this->assertGuest('web');
    $this->assertAuthenticated('customer');
});

test('the portal dashboard only counts the signed-in customer\'s data', function () {
    $customer = Customer::factory()->create();
    $user = portalUser($customer);
    Project::factory()->count(2)->for($customer)->create();
    Project::factory()->count(3)->create(); // other customers

    $this->actingAs($user, 'customer');

    $count = Livewire::test(PortalDashboard::class)->viewData('customer')->projects()->count();

    expect($count)->toBe(2);
});

test('a portal user cannot open another customer\'s project', function () {
    $user = portalUser();
    $otherProject = Project::factory()->create();

    $this->actingAs($user, 'customer');

    Livewire::test(PortalProjectShow::class, ['project' => $otherProject])
        ->assertStatus(404);
});

test('the portal never exposes internal documents', function () {
    $customer = Customer::factory()->create();
    $user = portalUser($customer);
    $project = Project::factory()->for($customer)->create();
    Document::factory()->for($project)->create(['is_internal' => true, 'title' => 'Secret costings']);
    Document::factory()->for($project)->shared()->create(['title' => 'Wireframes']);

    $this->actingAs($user, 'customer');

    Livewire::test(PortalProjectShow::class, ['project' => $project])
        ->assertSee('Wireframes')
        ->assertDontSee('Secret costings');
});

test('a portal user can approve a quotation', function () {
    $customer = Customer::factory()->create();
    $user = portalUser($customer);
    $quotation = Quotation::factory()->for($customer)->status(QuotationStatus::Sent)->create();

    $this->actingAs($user, 'customer');

    Livewire::test(PortalQuotations::class)
        ->call('view', $quotation->id)
        ->call('decide', 'approved')
        ->assertHasNoErrors();

    expect($quotation->fresh()->status)->toBe(QuotationStatus::Approved);
});

test('a portal user can submit a support ticket for their company', function () {
    $customer = Customer::factory()->create();
    $user = portalUser($customer);

    $this->actingAs($user, 'customer');

    Livewire::test(PortalTickets::class)
        ->set('subject', 'Please add another login')
        ->set('body', 'We hired a new manager who needs access.')
        ->set('priority', 'medium')
        ->call('create')
        ->assertHasNoErrors();

    $ticket = SupportTicket::firstWhere('subject', 'Please add another login');

    expect($ticket->customer_id)->toBe($customer->id)
        ->and($ticket->opened_by_user_id)->toBe($user->id);
});

test('portal replies exclude internal staff notes', function () {
    $customer = Customer::factory()->create();
    $user = portalUser($customer);
    $ticket = SupportTicket::factory()->for($customer)->create();
    $ticket->replies()->create(['body' => 'Public answer', 'is_internal' => false]);
    $ticket->replies()->create(['body' => 'Internal chatter', 'is_internal' => true]);

    $this->actingAs($user, 'customer');

    Livewire::test(PortalTickets::class)
        ->call('select', $ticket->id)
        ->assertSee('Public answer')
        ->assertDontSee('Internal chatter');
});
