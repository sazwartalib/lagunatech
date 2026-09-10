<?php

use App\Actions\Quotations\ConvertQuotationToProject;
use App\Actions\Quotations\TransitionQuotationStatus;
use App\Enums\QuotationStatus;
use App\Enums\Role;
use App\Livewire\Quotations\QuotationForm;
use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

test('a quotation is created with computed totals', function () {
    actingAsRole(Role::ProjectManager);
    $customer = Customer::factory()->create();

    Livewire::test(QuotationForm::class)
        ->set('form.customer_id', $customer->id)
        ->set('form.title', 'Website build')
        ->set('form.items', [
            ['description' => 'Design', 'quantity' => 1, 'unit_price' => 3000],
            ['description' => 'Development', 'quantity' => 2, 'unit_price' => 4000],
        ])
        ->set('form.discount_type', 'amount')
        ->set('form.discount_value', 1000)
        ->set('form.tax_rate', 10)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $quotation = Quotation::firstWhere('title', 'Website build');

    // subtotal 11000, -1000 discount = 10000, +10% tax = 11000
    expect($quotation->reference)->toMatch('/^QT-\d{4}-\d{3}$/')
        ->and((float) $quotation->subtotal)->toBe(11000.0)
        ->and((float) $quotation->discount_amount)->toBe(1000.0)
        ->and((float) $quotation->tax_amount)->toBe(1000.0)
        ->and((float) $quotation->total)->toBe(11000.0)
        ->and($quotation->items)->toHaveCount(2);
});

test('a percentage discount is applied before tax', function () {
    $quotation = Quotation::factory()->create([
        'discount_type' => 'percent',
        'discount_value' => 10,
        'tax_rate' => 0,
    ]);
    $quotation->items()->delete();
    $quotation->items()->create(['description' => 'X', 'quantity' => 1, 'unit_price' => 5000, 'position' => 0]);

    $quotation->load('items')->recalculateTotals();

    expect((float) $quotation->discount_amount)->toBe(500.0)
        ->and((float) $quotation->total)->toBe(4500.0);
});

test('approving a quotation notifies the deciders and stamps the decision time', function () {
    actingAsRole(Role::Finance);
    $decider = userWithRole(Role::ProjectManager);

    $quotation = Quotation::factory()->status(QuotationStatus::Sent)->create();

    app(TransitionQuotationStatus::class)->handle($quotation, QuotationStatus::Approved);
    $quotation->refresh();

    expect($quotation->status)->toBe(QuotationStatus::Approved)
        ->and($quotation->decided_at)->not->toBeNull()
        ->and($decider->fresh()->notifications)->not->toBeEmpty();
});

test('an illegal status transition is rejected', function () {
    $quotation = Quotation::factory()->status(QuotationStatus::Draft)->create();

    expect(fn () => app(TransitionQuotationStatus::class)->handle($quotation, QuotationStatus::Approved))
        ->toThrow(ValidationException::class);
});

test('an approved quotation converts to a project and links back', function () {
    actingAsRole(Role::ProjectManager);
    $quotation = Quotation::factory()->approved()->create(['title' => 'Portal build']);

    $project = app(ConvertQuotationToProject::class)->handle($quotation);

    expect($project->reference)->toMatch('/^LT-\d{4}-\d{3}$/')
        ->and($project->customer_id)->toBe($quotation->customer_id)
        ->and((float) $project->value)->toBe((float) $quotation->total)
        ->and($quotation->fresh()->project_id)->toBe($project->id);
});

test('converting the same quotation twice returns the original project', function () {
    actingAsRole(Role::ProjectManager);
    $quotation = Quotation::factory()->approved()->create();
    $action = app(ConvertQuotationToProject::class);

    $first = $action->handle($quotation);
    $second = $action->handle($quotation->fresh());

    expect($second->id)->toBe($first->id);
});

test('a non-approved quotation cannot be converted', function () {
    actingAsRole(Role::ProjectManager);
    $quotation = Quotation::factory()->status(QuotationStatus::Sent)->create();

    expect(fn () => app(ConvertQuotationToProject::class)->handle($quotation))
        ->toThrow(ValidationException::class);
});

test('a developer cannot open the quotation form', function () {
    $this->actingAs(userWithRole(Role::Developer));

    Livewire::test(QuotationForm::class)->assertForbidden();
});

test('the quotation PDF is served', function () {
    actingAsRole(Role::Finance);
    $quotation = Quotation::factory()->create();

    $this->get(route('quotations.pdf', $quotation))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
