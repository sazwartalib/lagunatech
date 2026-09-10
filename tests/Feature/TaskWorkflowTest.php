<?php

use App\Enums\Role;
use App\Enums\TaskStatus;
use App\Livewire\Projects\ProjectShow;
use App\Livewire\Tasks\TaskIndex;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

test('a task can be added to a project from its board', function () {
    actingAsRole(Role::ProjectManager);
    $project = Project::factory()->create();

    Livewire::test(ProjectShow::class, ['project' => $project])
        ->set('newTaskTitle', 'Wire up the payment webhook')
        ->set('newTaskPriority', 'high')
        ->call('addTask')
        ->assertHasNoErrors();

    expect($project->tasks()->count())->toBe(1)
        ->and($project->tasks()->first())
        ->title->toBe('Wire up the payment webhook')
        ->status->toBe(TaskStatus::Todo);
});

test('moving a task to done stamps the completion time', function () {
    actingAsRole(Role::ProjectManager);
    $project = Project::factory()->create();
    $task = Task::factory()->for($project)->status(TaskStatus::InProgress)->create();

    Livewire::test(ProjectShow::class, ['project' => $project])
        ->call('moveTask', $task->id, TaskStatus::Done->value);

    $task->refresh();

    expect($task->status)->toBe(TaskStatus::Done)
        ->and($task->completed_at)->not->toBeNull();
});

test('the assignee can tick their own task done from the task list', function () {
    $user = userWithRole(Role::Developer);
    $this->actingAs($user);

    $task = Task::factory()->status(TaskStatus::Todo)->create(['assignee_id' => $user->id]);

    Livewire::test(TaskIndex::class)
        ->call('toggleDone', $task->id);

    expect($task->refresh()->status)->toBe(TaskStatus::Done);
});

test('a user without task permission cannot add tasks', function () {
    $stranger = User::factory()->create();
    $stranger->assignRole(Role::Finance->value);
    $this->actingAs($stranger);

    $project = Project::factory()->create();

    Livewire::test(ProjectShow::class, ['project' => $project])
        ->set('newTaskTitle', 'Sneaky task')
        ->call('addTask')
        ->assertForbidden();
});
