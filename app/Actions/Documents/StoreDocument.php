<?php

namespace App\Actions\Documents;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\UploadedFile;

class StoreDocument
{
    /**
     * Persist an uploaded file to private storage and record it against a project.
     */
    public function handle(Project $project, UploadedFile $file, string $category, ?string $title, bool $internal): Document
    {
        $disk = 'local';
        $path = $file->store("documents/{$project->id}", $disk);

        return $project->documents()->create([
            'uploaded_by' => auth()->id(),
            'title' => $title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'category' => $category,
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'is_internal' => $internal,
        ]);
    }
}
