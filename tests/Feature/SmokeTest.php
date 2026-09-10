<?php

use App\Enums\ChangeRequestStatus;
use App\Enums\QuotationStatus;
use App\Enums\Role;
use App\Models\Bug;
use App\Models\ChangeRequest;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Invoice;
use App\Models\MaintenancePlan;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SupportTicket;
use App\Models\User;

/**
 * Every primary screen should render without error for an admin.
 */
test('core pages render for an admin', function (string $routeName) {
    actingAsRole(Role::Admin);

    $customer = Customer::factory()->create();
    $project = Project::factory()->for($customer)->create();
    $quotation = Quotation::factory()->for($customer)->status(QuotationStatus::Draft)->create();
    $invoice = Invoice::factory()->for($customer)->create();
    $changeRequest = ChangeRequest::factory()->for($project)
        ->status(ChangeRequestStatus::Pending)
        ->create(['customer_id' => $customer->id]);
    $bug = Bug::factory()->for($project)->create();
    $meeting = Meeting::factory()->for($project)->create();
    $ticket = SupportTicket::factory()->for($customer)->create();
    $staff = User::factory()->create();
    $plan = MaintenancePlan::factory()->for($customer)->create();

    $params = match (true) {
        str_starts_with($routeName, 'customers.') && $routeName !== 'customers.index' => $customer,
        str_starts_with($routeName, 'projects.') && ! in_array($routeName, ['projects.index', 'projects.mine', 'projects.create'], true) => $project,
        str_starts_with($routeName, 'quotations.') && ! in_array($routeName, ['quotations.index', 'quotations.create'], true) => $quotation,
        str_starts_with($routeName, 'invoices.') && ! in_array($routeName, ['invoices.index', 'invoices.outstanding', 'invoices.create'], true) => $invoice,
        str_starts_with($routeName, 'change-requests.') && ! in_array($routeName, ['change-requests.index', 'change-requests.create'], true) => $changeRequest,
        str_starts_with($routeName, 'bugs.') && ! in_array($routeName, ['bugs.index', 'bugs.create'], true) => $bug,
        str_starts_with($routeName, 'meetings.') && ! in_array($routeName, ['meetings.index', 'meetings.create'], true) => $meeting,
        $routeName === 'support.show' => $ticket,
        $routeName === 'staff.edit' => ['staff' => $staff],
        $routeName === 'maintenance.edit' => ['plan' => $plan],
        default => [],
    };

    $this->get(route($routeName, $params))->assertOk();
})->with([
    'dashboard',
    'customers.index',
    'customers.create',
    'customers.show',
    'customers.edit',
    'projects.index',
    'projects.mine',
    'projects.create',
    'projects.show',
    'projects.edit',
    'tasks.index',
    'quotations.index',
    'quotations.create',
    'quotations.show',
    'quotations.edit',
    'invoices.index',
    'invoices.outstanding',
    'invoices.create',
    'invoices.show',
    'invoices.edit',
    'payments.index',
    'change-requests.index',
    'change-requests.create',
    'change-requests.show',
    'change-requests.edit',
    'bugs.index',
    'bugs.create',
    'bugs.show',
    'bugs.edit',
    'meetings.index',
    'meetings.create',
    'meetings.show',
    'meetings.edit',
    'communications.index',
    'calendar',
    'reports',
    'notifications.index',
    'support.index',
    'support.create',
    'support.show',
    'maintenance.index',
    'maintenance.create',
    'maintenance.edit',
    'staff.index',
    'staff.create',
    'staff.edit',
    'system.roles',
    'system.activity',
    'system.settings',
]);

test('portal pages render for a signed-in customer', function (string $routeName) {
    $customer = Customer::factory()->create();
    $user = CustomerUser::factory()->create(['customer_id' => $customer->id]);
    $project = Project::factory()->for($customer)->create();

    $this->actingAs($user, 'customer');

    $params = str_contains($routeName, 'projects.show') ? $project : [];

    $this->get(route($routeName, $params))->assertOk();
})->with([
    'portal.dashboard',
    'portal.projects',
    'portal.projects.show',
    'portal.quotations',
    'portal.invoices',
    'portal.tickets',
]);
