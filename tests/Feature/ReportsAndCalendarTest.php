<?php

use App\Enums\Role;
use App\Livewire\Calendar\CalendarView;
use App\Livewire\Reports\ReportsOverview;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

test('reports are visible to finance but not to a developer', function () {
    actingAsRole(Role::Finance);
    Livewire::test(ReportsOverview::class)->assertOk();

    $this->actingAs(userWithRole(Role::Developer));
    Livewire::test(ReportsOverview::class)->assertForbidden();
});

test('the project report counts active and overdue projects', function () {
    actingAsRole(Role::Admin);

    Project::factory()->count(2)->create(['status' => 'development', 'target_end_date' => now()->addMonth()]);
    Project::factory()->create([
        'status' => 'development',
        'start_date' => now()->subMonths(2),
        'target_end_date' => now()->subWeek(),
        'progress' => 30,
    ]);
    Project::factory()->create(['status' => 'completed']);

    $report = Livewire::test(ReportsOverview::class)->instance()->projectReport();

    expect($report['active'])->toBe(3)
        ->and($report['completed'])->toBe(1)
        ->and($report['overdue'])->toBe(1);
});

test('the staff report surfaces overdue task counts', function () {
    actingAsRole(Role::Admin);
    $dev = User::factory()->create(['name' => 'Overloaded Dev']);

    Task::factory()->count(2)->create([
        'assignee_id' => $dev->id,
        'status' => 'in_progress',
        'due_date' => now()->subDays(3),
    ]);

    $rows = Livewire::test(ReportsOverview::class)->instance()->staffReport();
    $row = $rows->firstWhere('name', 'Overloaded Dev');

    expect($row['overdue_tasks'])->toBe(2);
});

test('the calendar renders and includes a meeting on its day', function () {
    actingAsRole(Role::ProjectManager);

    $meeting = Meeting::factory()->create([
        'scheduled_at' => now()->startOfMonth()->addDays(10)->setTime(9, 0),
        'title' => 'Board Review',
    ]);

    Livewire::test(CalendarView::class)
        ->assertOk()
        ->assertSee('Board Review');
});

test('the calendar can move to the next month', function () {
    actingAsRole(Role::ProjectManager);

    Livewire::test(CalendarView::class)
        ->call('shiftMonth', 1)
        ->assertSet('month', now()->addMonth()->startOfMonth()->toDateString());
});
