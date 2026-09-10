<?php

namespace App\Actions\Bugs;

use App\Enums\BugStatus;
use App\Models\Bug;
use App\Support\ReferenceGenerator;

class UpsertBug
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?Bug $bug = null): Bug
    {
        $bug ??= new Bug;

        if (! $bug->exists) {
            $bug->reference = $this->references->bugReference();
            $bug->reported_by = auth()->id();
        }

        $bug->fill($data);

        // Keep resolved_at in step with the status.
        if ($bug->isDirty('status')) {
            $bug->resolved_at = $bug->status->isResolved() ? ($bug->resolved_at ?? now()) : null;
        }

        $bug->save();

        return $bug->refresh();
    }

    public function changeStatus(Bug $bug, BugStatus $status): Bug
    {
        $bug->status = $status;
        $bug->resolved_at = $status->isResolved() ? ($bug->resolved_at ?? now()) : null;
        $bug->save();

        return $bug;
    }
}
