<?php

namespace App\Http\Controllers;

use App\Models\LeadAttachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadAttachmentDownloadController extends Controller
{
    public function __invoke(LeadAttachment $leadAttachment): StreamedResponse
    {
        $this->authorize('view', $leadAttachment->lead);

        abort_unless(Storage::disk($leadAttachment->disk)->exists($leadAttachment->path), 404);

        return Storage::disk($leadAttachment->disk)->download($leadAttachment->path, $leadAttachment->original_name);
    }
}
