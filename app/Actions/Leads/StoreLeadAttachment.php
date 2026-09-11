<?php

namespace App\Actions\Leads;

use App\Enums\LeadAttachmentKind;
use App\Models\Lead;
use App\Models\LeadAttachment;
use Illuminate\Http\UploadedFile;

class StoreLeadAttachment
{
    /**
     * Persist an uploaded reference file or logo to private storage against a lead.
     */
    public function handle(Lead $lead, UploadedFile $file, LeadAttachmentKind $kind): LeadAttachment
    {
        $disk = 'local';
        $path = $file->store("leads/{$lead->id}", $disk);

        return $lead->attachments()->create([
            'kind' => $kind,
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}
