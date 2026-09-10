<?php

namespace App\Actions\Projects;

use App\Models\Project;
use App\Support\ReferenceGenerator;

class UpsertProject
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $memberIds
     */
    public function handle(array $data, ?Project $project = null, array $memberIds = []): Project
    {
        $project ??= new Project;

        if (! $project->exists) {
            $project->reference = $this->references->projectReference();
        }

        $project->fill($data)->save();

        $project->members()->sync($memberIds);

        return $project->refresh();
    }
}
