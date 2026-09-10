<?php

use App\Actions\Bugs\UpsertBug;
use App\Enums\BugStatus;
use App\Enums\Role;
use App\Livewire\Bugs\BugForm;
use App\Livewire\Bugs\BugShow;
use App\Models\Bug;
use App\Models\Project;
use Livewire\Livewire;

test('a bug is logged with a BUG reference', function () {
    actingAsRole(Role::Developer);
    $project = Project::factory()->create();

    Livewire::test(BugForm::class)
        ->set('form.project_id', $project->id)
        ->set('form.title', 'Login button does nothing on mobile')
        ->set('form.priority', 'high')
        ->set('form.status', BugStatus::Open->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $bug = Bug::firstWhere('title', 'Login button does nothing on mobile');

    expect($bug->reference)->toMatch('/^BUG-\d{4}-\d{3}$/')
        ->and($bug->reported_by)->not->toBeNull();
});

test('moving a bug to fixed stamps the resolved time and back to open clears it', function () {
    $bug = Bug::factory()->status(BugStatus::InProgress)->create();
    $action = app(UpsertBug::class);

    $action->changeStatus($bug, BugStatus::Fixed);
    expect($bug->fresh()->resolved_at)->not->toBeNull();

    $action->changeStatus($bug, BugStatus::Reopened);
    expect($bug->fresh()->resolved_at)->toBeNull();
});

test('an open bug past its due date is flagged overdue', function () {
    $bug = Bug::factory()->overdue()->create();

    expect($bug->is_overdue)->toBeTrue()
        ->and(Bug::query()->critical()->pluck('id'))->toContain($bug->id);
});

test('the assignee can change the status of their own bug', function () {
    // Finance lacks "manage bugs", so this proves the assignee override.
    $assignee = userWithRole(Role::Finance);
    $this->actingAs($assignee);

    $bug = Bug::factory()->status(BugStatus::Open)->create(['assigned_to' => $assignee->id]);

    Livewire::test(BugShow::class, ['bug' => $bug])
        ->call('setStatus', BugStatus::InProgress->value)
        ->assertHasNoErrors();

    expect($bug->fresh()->status)->toBe(BugStatus::InProgress);
});

test('a comment can be added to a bug', function () {
    actingAsRole(Role::Developer);
    $bug = Bug::factory()->create();

    Livewire::test(BugShow::class, ['bug' => $bug])
        ->set('comment', 'Reproduced on Safari 17. Looking into it.')
        ->call('addComment')
        ->assertHasNoErrors();

    expect($bug->comments()->count())->toBe(1);
});
