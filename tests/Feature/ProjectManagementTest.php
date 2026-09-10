<?php

use App\Enums\ProjectHealth;
use App\Enums\ProjectStatus;
use App\Enums\Role;
use App\Livewire\Projects\ProjectForm;
use App\Livewire\Projects\ProjectIndex;
use App\Models\Customer;
use App\Models\Project;
use Livewire\Livewire;

test('a project can be created and gets an LT reference', function () {
    actingAsRole(Role::ProjectManager);
    $customer = Customer::factory()->create();

    Livewire::test(ProjectForm::class)
        ->set('form.name', 'Booking Platform')
        ->set('form.customer_id', $customer->id)
        ->set('form.status', ProjectStatus::Planning->value)
        ->set('form.priority', 'high')
        ->set('form.progress', 10)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $project = Project::firstWhere('name', 'Booking Platform');

    expect($project->reference)->toMatch('/^LT-\d{4}-\d{3}$/')
        ->and($project->customer_id)->toBe($customer->id);
});

test('project creation preselects the customer from the query string', function () {
    actingAsRole(Role::ProjectManager);
    $customer = Customer::factory()->create();

    Livewire::withQueryParams(['customer' => $customer->id])
        ->test(ProjectForm::class)
        ->assertSet('form.customer_id', $customer->id);
});

test('a developer may not create projects', function () {
    $this->actingAs(userWithRole(Role::Developer));

    Livewire::test(ProjectForm::class)->assertForbidden();
});

test('an overdue active project is flagged as overdue', function () {
    $project = Project::factory()->create([
        'status' => ProjectStatus::Development,
        'start_date' => now()->subMonths(2),
        'target_end_date' => now()->subWeek(),
        'progress' => 50,
    ]);

    expect($project->health)->toBe(ProjectHealth::Overdue)
        ->and($project->is_overdue)->toBeTrue();
});

test('a completed project is never overdue', function () {
    $project = Project::factory()->create([
        'status' => ProjectStatus::Completed,
        'target_end_date' => now()->subMonth(),
        'progress' => 100,
    ]);

    expect($project->health)->toBe(ProjectHealth::Completed)
        ->and($project->is_overdue)->toBeFalse();
});

test('a project with plenty of runway and matching progress is on track', function () {
    $project = Project::factory()->create([
        'status' => ProjectStatus::Development,
        'start_date' => now()->subDays(10),
        'target_end_date' => now()->addMonths(2),
        'progress' => 40,
    ]);

    expect($project->health)->toBe(ProjectHealth::OnTrack);
});

test('the my-projects scope only shows the user\'s projects', function () {
    $user = userWithRole(Role::ProjectManager);
    $this->actingAs($user);

    $mine = Project::factory()->create(['lead_id' => $user->id]);
    $theirs = Project::factory()->create();

    Livewire::test(ProjectIndex::class, ['mineOnly' => true])
        ->assertSee($mine->reference)
        ->assertDontSee($theirs->reference);
});
