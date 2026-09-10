<?php

namespace App\Actions\Meetings;

use App\Models\Meeting;
use Illuminate\Support\Facades\DB;

class UpsertMeeting
{
    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $participantIds
     * @param  list<array{description: string, owner_id: int|null, due_date: string|null}>  $actionItems
     */
    public function handle(array $data, array $participantIds, array $actionItems, ?Meeting $meeting = null): Meeting
    {
        return DB::transaction(function () use ($data, $participantIds, $actionItems, $meeting): Meeting {
            $meeting ??= new Meeting;

            if (! $meeting->exists) {
                $meeting->created_by = auth()->id();
            }

            $meeting->fill($data)->save();

            // Participants — replace the staff set, keep external attendees.
            $meeting->participants()->whereNotNull('user_id')->delete();
            foreach ($participantIds as $userId) {
                $meeting->participants()->create(['user_id' => $userId]);
            }

            // Action items — replace those not yet converted to tasks.
            $meeting->actionItems()->whereNull('task_id')->delete();
            foreach ($actionItems as $item) {
                $meeting->actionItems()->create([
                    'description' => $item['description'],
                    'owner_id' => $item['owner_id'] ?: null,
                    'due_date' => $item['due_date'] ?: null,
                ]);
            }

            return $meeting->refresh();
        });
    }
}
