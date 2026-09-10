<?php

namespace App\Actions\ChangeRequests;

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Support\ReferenceGenerator;

class UpsertChangeRequest
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?ChangeRequest $changeRequest = null): ChangeRequest
    {
        $changeRequest ??= new ChangeRequest;

        if (! $changeRequest->exists) {
            $changeRequest->reference = $this->references->changeRequestReference();
            $changeRequest->requested_by = auth()->id();
            // A change request always belongs to the project's customer.
            $changeRequest->customer_id = Project::whereKey($data['project_id'])->value('customer_id');
        }

        $changeRequest->fill($data)->save();

        return $changeRequest->refresh();
    }
}
