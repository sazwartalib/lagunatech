<?php

use App\Actions\Meetings\ConvertActionItemToTask;
use App\Enums\MeetingStatus;
use App\Enums\Role;
use App\Livewire\Meetings\MeetingForm;
use App\Livewire\Meetings\MeetingShow;
use App\Models\Meeting;
use App\Models\MeetingActionItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

test('a meeting is scheduled with participants and action items', function () {
    actingAsRole(Role::ProjectManager);
    $project = Project::factory()->create();
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    Livewire::test(MeetingForm::class)
        ->set('form.title', 'Kick-off')
        ->set('form.project_id', $project->id)
        ->set('form.scheduled_at', now()->addDays(2)->format('Y-m-d\TH:i'))
        ->set('form.duration_minutes', 45)
        ->set('form.participant_ids', [$alice->id, $bob->id])
        ->set('form.action_items', [
            ['description' => 'Share the SOW', 'owner_id' => $alice->id, 'due_date' => null],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    $meeting = Meeting::firstWhere('title', 'Kick-off');

    expect($meeting->participants)->toHaveCount(2)
        ->and($meeting->actionItems)->toHaveCount(1);
});

test('a meeting action item converts to a task on the linked project', function () {
    actingAsRole(Role::ProjectManager);
    $project = Project::factory()->create();
    $meeting = Meeting::factory()->create(['project_id' => $project->id]);
    $item = MeetingActionItem::factory()->create(['meeting_id' => $meeting->id]);

    $task = app(ConvertActionItemToTask::class)->handle($item);

    expect($task->project_id)->toBe($project->id)
        ->and($item->fresh()->task_id)->toBe($task->id);
});

test('an action item on a project-less meeting cannot become a task', function () {
    actingAsRole(Role::ProjectManager);
    $meeting = Meeting::factory()->create(['project_id' => null]);
    $item = MeetingActionItem::factory()->create(['meeting_id' => $meeting->id]);

    expect(fn () => app(ConvertActionItemToTask::class)->handle($item))
        ->toThrow(ValidationException::class);
});

test('a meeting can be marked complete with notes', function () {
    actingAsRole(Role::ProjectManager);
    $meeting = Meeting::factory()->create(['status' => MeetingStatus::Scheduled]);

    Livewire::test(MeetingShow::class, ['meeting' => $meeting])
        ->set('notes', 'Agreed go-live date is the 30th.')
        ->call('complete');

    expect($meeting->fresh())
        ->status->toBe(MeetingStatus::Completed)
        ->notes->toContain('go-live');
});
