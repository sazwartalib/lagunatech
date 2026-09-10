<?php

use App\Enums\Role;
use App\Livewire\Customers\CustomerForm;
use App\Livewire\Customers\CustomerIndex;
use App\Models\Customer;
use App\Models\User;
use Livewire\Livewire;

test('a user with no role cannot view the customer list', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('customers.index'))->assertForbidden();
});

test('an authorised user sees the customer list', function () {
    actingAsRole(Role::ProjectManager);
    Customer::factory()->count(3)->create();

    Livewire::test(CustomerIndex::class)
        ->assertOk()
        ->assertSee('Customers');
});

test('a customer can be created and gets a reference', function () {
    actingAsRole(Role::Admin);

    Livewire::test(CustomerForm::class)
        ->set('form.company_name', 'Acme Widgets Sdn Bhd')
        ->set('form.email', 'hello@acme.test')
        ->set('form.status', 'active')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $customer = Customer::firstWhere('company_name', 'Acme Widgets Sdn Bhd');

    expect($customer)->not->toBeNull()
        ->and($customer->reference)->toMatch('/^CUST-\d{4}-\d{3}$/')
        ->and($customer->status->value)->toBe('active');
});

test('creating a customer requires a company name', function () {
    actingAsRole(Role::Admin);

    Livewire::test(CustomerForm::class)
        ->set('form.company_name', '')
        ->call('save')
        ->assertHasErrors(['form.company_name' => 'required']);
});

test('a developer may not create customers', function () {
    $this->actingAs(userWithRole(Role::Developer));

    Livewire::test(CustomerForm::class)->assertForbidden();
});

test('the customer list can be searched', function () {
    actingAsRole(Role::Admin);
    Customer::factory()->create(['company_name' => 'Findable Trading']);
    Customer::factory()->create(['company_name' => 'Hidden Holdings']);

    Livewire::test(CustomerIndex::class)
        ->set('search', 'Findable')
        ->assertSee('Findable Trading')
        ->assertDontSee('Hidden Holdings');
});
