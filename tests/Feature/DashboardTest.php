<?php

use App\Enums\ProjectStatus;
use App\Enums\Role;
use App\Livewire\Dashboard;
use App\Models\Project;
use App\Models\Task;
use Livewire\Livewire;

test('the dashboard renders with a greeting', function () {
    actingAsRole(Role::Admin);

    Livewire::test(Dashboard::class)
        ->assertOk()
        ->assertSeeHtml('👋');
});

test('the dashboard counts active and overdue projects', function () {
    actingAsRole(Role::Admin);

    Project::factory()->count(2)->create(['status' => ProjectStatus::Development, 'target_end_date' => now()->addMonth()]);
    Project::factory()->create([
        'status' => ProjectStatus::Development,
        'start_date' => now()->subMonths(2),
        'target_end_date' => now()->subWeek(),
        'progress' => 40,
    ]);
    Project::factory()->create(['status' => ProjectStatus::Completed]);

    $stats = Livewire::test(Dashboard::class)->instance()->stats();

    expect($stats['active_projects'])->toBe(3)
        ->and($stats['overdue'])->toBe(1);
});

test('a signed-in user sees their own open tasks', function () {
    $user = actingAsRole(Role::Developer);

    Task::factory()->create(['assignee_id' => $user->id, 'status' => 'todo']);
    Task::factory()->create(['assignee_id' => $user->id, 'status' => 'done']);
    Task::factory()->create(['status' => 'todo']);

    $myTasks = Livewire::test(Dashboard::class)->instance()->myTasks();

    expect($myTasks)->toHaveCount(1);
});
