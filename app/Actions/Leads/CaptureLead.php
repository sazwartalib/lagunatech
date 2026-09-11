<?php

namespace App\Actions\Leads;

use App\Enums\LeadAttachmentKind;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\Role;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadReceived;
use App\Support\ReferenceGenerator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

/**
 * Turns a public website idea/enquiry into a tracked lead, stores any
 * supporting files, and alerts the sales team.
 */
class CaptureLead
{
    public function __construct(
        private readonly ReferenceGenerator $references,
        private readonly StoreLeadAttachment $attachments,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<UploadedFile>  $files  Supporting reference docs/images.
     */
    public function handle(array $data, ?string $ipAddress = null, ?UploadedFile $logo = null, array $files = []): Lead
    {
        $lead = Lead::create([
            'reference' => $this->references->leadReference(),
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'project_type' => $data['project_type'] ?? null,
            'budget_range' => $data['budget_range'] ?? null,
            'color_theme' => $data['color_theme'] ?? null,
            'slogan' => $data['slogan'] ?? null,
            'message' => $data['message'] ?? null,
            'source' => $data['source'] ?? LeadSource::Website->value,
            'status' => LeadStatus::New->value,
            'ip_address' => $ipAddress,
        ]);

        if ($logo) {
            $this->attachments->handle($lead, $logo, LeadAttachmentKind::Logo);
        }

        foreach ($files as $file) {
            $this->attachments->handle($lead, $file, LeadAttachmentKind::Attachment);
        }

        $recipients = User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', [
                Role::Admin->value,
                Role::ProjectManager->value,
                Role::SuperAdmin->value,
            ]))
            ->get();

        Notification::send($recipients, new NewLeadReceived($lead));

        return $lead;
    }
}
