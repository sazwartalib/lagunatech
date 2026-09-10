<?php

use App\Actions\ChangeRequests\ConvertChangeRequestToTask;
use App\Enums\ChangeRequestStatus;
use App\Enums\Role;
use App\Livewire\ChangeRequests\ChangeRequestForm;
use App\Models\ChangeRequest;
use App\Models\Project;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

test('a change request is created with a reference and inherits the project customer', function () {
    actingAsRole(Role::ProjectManager);
    $project = Project::factory()->create();

    Livewire::test(ChangeRequestForm::class)
        ->set('form.project_id', $project->id)
        ->set('form.title', 'Add WhatsApp notifications')
        ->set('form.estimated_cost', 2500)
        ->set('form.additional_days', 5)
        ->set('form.status', ChangeRequestStatus::Pending->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $cr = ChangeRequest::firstWhere('title', 'Add WhatsApp notifications');

    expect($cr->reference)->toMatch('/^CR-\d{4}-\d{3}$/')
        ->and($cr->customer_id)->toBe($project->customer_id)
        ->and((float) $cr->estimated_cost)->toBe(2500.0);
});

test('an approved change request converts into a project task', function () {
    actingAsRole(Role::ProjectManager);
    $cr = ChangeRequest::factory()->approved()->create();

    $task = app(ConvertChangeRequestToTask::class)->handle($cr);

    expect($task->project_id)->toBe($cr->project_id)
        ->and($task->title)->toStartWith('[CR]')
        ->and($cr->fresh()->task_id)->toBe($task->id);
});

test('converting a change request is idempotent', function () {
    actingAsRole(Role::ProjectManager);
    $cr = ChangeRequest::factory()->approved()->create();
    $action = app(ConvertChangeRequestToTask::class);

    $first = $action->handle($cr);
    $second = $action->handle($cr->fresh());

    expect($second->id)->toBe($first->id);
});

test('a non-approved change request cannot become a task', function () {
    actingAsRole(Role::ProjectManager);
    $cr = ChangeRequest::factory()->status(ChangeRequestStatus::Quoted)->create();

    expect(fn () => app(ConvertChangeRequestToTask::class)->handle($cr))
        ->toThrow(ValidationException::class);
});

test('a designer cannot create change requests', function () {
    $this->actingAs(userWithRole(Role::Designer));

    Livewire::test(ChangeRequestForm::class)->assertForbidden();
});
