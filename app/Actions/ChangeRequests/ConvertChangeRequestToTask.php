<?php

namespace App\Actions\ChangeRequests;

use App\Enums\ChangeRequestStatus;
use App\Enums\TaskStatus;
use App\Models\ChangeRequest;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Promotes an approved change request into a tracked task on its project,
 * so agreed extra scope enters the normal delivery workflow.
 */
class ConvertChangeRequestToTask
{
    public function handle(ChangeRequest $changeRequest): Task
    {
        if ($changeRequest->status !== ChangeRequestStatus::Approved) {
            throw ValidationException::withMessages([
                'status' => 'Only an approved change request can become a task.',
            ]);
        }

        if ($changeRequest->task_id !== null) {
            return $changeRequest->task;
        }

        return DB::transaction(function () use ($changeRequest): Task {
            $task = Task::create([
                'project_id' => $changeRequest->project_id,
                'title' => '[CR] '.$changeRequest->title,
                'description' => trim(($changeRequest->description ?? '')."\n\nFrom change request {$changeRequest->reference}."),
                'assignee_id' => $changeRequest->assigned_to,
                'created_by' => auth()->id(),
                'status' => TaskStatus::Todo,
                'priority' => 'medium',
                'estimated_hours' => $changeRequest->additional_days ? $changeRequest->additional_days * 8 : null,
            ]);

            $changeRequest->update(['task_id' => $task->id]);

            return $task;
        });
    }
}
