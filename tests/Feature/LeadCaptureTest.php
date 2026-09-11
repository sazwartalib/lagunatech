<?php

use App\Actions\Leads\ConvertLeadToCustomer;
use App\Enums\LeadStatus;
use App\Enums\Role;
use App\Livewire\Leads\LeadShow;
use App\Livewire\Marketing\LeadForm;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadReceived;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

test('the public landing page renders without authentication', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Tell us about your project')
        ->assertSeeLivewire(LeadForm::class);
});

test('a signed-in staff member is sent to the dashboard from the root', function () {
    $this->actingAs(userWithRole(Role::Admin));

    $this->get('/')->assertRedirect(route('dashboard'));
});

test('the contact form captures a lead and notifies the sales team', function () {
    Notification::fake();
    $sales = userWithRole(Role::ProjectManager);

    Livewire::test(LeadForm::class)
        ->set('name', 'Farah Idris')
        ->set('company', 'Idris Logistics')
        ->set('email', 'farah@idris.my')
        ->set('phone', '012-3456789')
        ->set('project_type', 'Custom Software System')
        ->set('message', 'We need a fleet management system.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $lead = Lead::firstWhere('email', 'farah@idris.my');

    expect($lead)->not->toBeNull()
        ->and($lead->reference)->toMatch('/^LEAD-\d{4}-\d{3}$/')
        ->and($lead->status)->toBe(LeadStatus::New)
        ->and($lead->source->value)->toBe('website');

    Notification::assertSentTo($sales, NewLeadReceived::class);
});

test('the honeypot field silently rejects bot submissions', function () {
    Notification::fake();

    Livewire::test(LeadForm::class)
        ->set('name', 'Spam Bot')
        ->set('email', 'bot@spam.test')
        ->set('phone', '000')
        ->set('message', 'buy cheap stuff')
        ->set('website', 'http://spam.example')
        ->call('submit')
        ->assertSet('submitted', true);

    expect(Lead::count())->toBe(0);
    Notification::assertNothingSent();
});

test('the contact form is rate limited per IP', function () {
    RateLimiter::clear('lead-form:127.0.0.1');

    foreach (range(1, 3) as $i) {
        Livewire::test(LeadForm::class)
            ->set('name', "Person {$i}")
            ->set('email', "p{$i}@test.my")
            ->set('phone', '012-0000000')
            ->set('message', 'Enquiry number '.$i)
            ->call('submit')
            ->assertHasNoErrors();
    }

    Livewire::test(LeadForm::class)
        ->set('name', 'One Too Many')
        ->set('email', 'extra@test.my')
        ->set('phone', '012-0000000')
        ->set('message', 'Fourth attempt')
        ->call('submit')
        ->assertHasErrors('message');

    expect(Lead::count())->toBe(3);
});

test('missing required fields are rejected', function () {
    Livewire::test(LeadForm::class)
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'phone', 'message']);
});

test('a lead can be converted into a customer and is linked back', function () {
    actingAsRole(Role::ProjectManager);
    $lead = Lead::factory()->create([
        'company' => 'Meridian Retail',
        'name' => 'Suria Kamal',
        'email' => 'suria@meridian.my',
    ]);

    $customer = app(ConvertLeadToCustomer::class)->handle($lead);

    expect($customer)->toBeInstanceOf(Customer::class)
        ->and($customer->company_name)->toBe('Meridian Retail')
        ->and($customer->reference)->toMatch('/^CUST-\d{4}-\d{3}$/')
        ->and($lead->fresh()->status)->toBe(LeadStatus::Won)
        ->and($lead->fresh()->converted_customer_id)->toBe($customer->id);
});

test('converting an already-converted lead returns the same customer', function () {
    actingAsRole(Role::ProjectManager);
    $lead = Lead::factory()->create();
    $action = app(ConvertLeadToCustomer::class);

    $first = $action->handle($lead);
    $second = $action->handle($lead->fresh());

    expect($second->id)->toBe($first->id);
});

test('a developer cannot convert a lead', function () {
    $this->actingAs(userWithRole(Role::Developer));
    $lead = Lead::factory()->create();

    Livewire::test(LeadShow::class, ['lead' => $lead])
        ->call('convert')
        ->assertForbidden();
});

test('a follow-up note can be added to a lead', function () {
    actingAsRole(Role::ProjectManager);
    $lead = Lead::factory()->create();

    Livewire::test(LeadShow::class, ['lead' => $lead])
        ->set('note', 'Called and left a voicemail.')
        ->call('addNote')
        ->assertHasNoErrors();

    expect($lead->notes()->count())->toBe(1);
});

test('a user with no role cannot see the leads list', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('leads.index'))->assertForbidden();
});
