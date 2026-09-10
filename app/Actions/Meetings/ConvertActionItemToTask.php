<?php

namespace App\Actions\Meetings;

use App\Enums\TaskStatus;
use App\Models\MeetingActionItem;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConvertActionItemToTask
{
    public function handle(MeetingActionItem $item): Task
    {
        $item->loadMissing('meeting');

        if ($item->meeting->project_id === null) {
            throw ValidationException::withMessages([
                'project' => 'This meeting is not linked to a project, so its action items cannot become tasks.',
            ]);
        }

        if ($item->task_id !== null) {
            return $item->task;
        }

        return DB::transaction(function () use ($item): Task {
            $task = Task::create([
                'project_id' => $item->meeting->project_id,
                'title' => $item->description,
                'assignee_id' => $item->owner_id,
                'created_by' => auth()->id(),
                'status' => TaskStatus::Todo,
                'priority' => 'medium',
                'due_date' => $item->due_date,
            ]);

            $item->update(['task_id' => $task->id]);

            return $task;
        });
    }
}
