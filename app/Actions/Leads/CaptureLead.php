<?php

namespace App\Actions\Leads;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\Role;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\NewLeadReceived;
use App\Support\ReferenceGenerator;
use Illuminate\Support\Facades\Notification;

/**
 * Turns a public website enquiry into a tracked lead and alerts the sales team.
 */
class CaptureLead
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?string $ipAddress = null): Lead
    {
        $lead = Lead::create([
            'reference' => $this->references->leadReference(),
            'name' => $data['name'],
            'company' => $data['company'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'project_type' => $data['project_type'] ?? null,
            'budget_range' => $data['budget_range'] ?? null,
            'message' => $data['message'] ?? null,
            'source' => $data['source'] ?? LeadSource::Website->value,
            'status' => LeadStatus::New->value,
            'ip_address' => $ipAddress,
        ]);

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
