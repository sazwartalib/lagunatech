<?php

use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Models\Invoice;
use App\Models\Project;
use App\Notifications\InvoiceOverdue;
use App\Notifications\ProjectDeadlineApproaching;
use Illuminate\Support\Facades\Notification;

test('the reminder command notifies finance about overdue invoices', function () {
    Notification::fake();

    $finance = userWithRole(Role::Finance);
    Invoice::factory()->overdue()->create();

    $this->artisan('reminders:dispatch')->assertSuccessful();

    Notification::assertSentTo($finance, InvoiceOverdue::class);
});

test('the reminder command does not double-notify within the day', function () {
    Notification::fake();
    userWithRole(Role::Finance);
    Invoice::factory()->overdue()->create();

    $this->artisan('reminders:dispatch');
    $this->artisan('reminders:dispatch');

    Notification::assertSentTimes(InvoiceOverdue::class, 1);
});

test('the reminder command warns the project lead about a near deadline', function () {
    Notification::fake();

    $lead = userWithRole(Role::ProjectManager);
    Project::factory()->create([
        'lead_id' => $lead->id,
        'target_end_date' => now()->addDays(3),
        'status' => 'development',
    ]);

    $this->artisan('reminders:dispatch')->assertSuccessful();

    Notification::assertSentTo($lead, ProjectDeadlineApproaching::class);
});

test('a paid invoice is never flagged overdue by the reminder command', function () {
    Notification::fake();
    userWithRole(Role::Finance);

    Invoice::factory()->status(InvoiceStatus::Paid)->create(['due_date' => now()->subMonth()]);

    $this->artisan('reminders:dispatch');

    Notification::assertNothingSent();
});
