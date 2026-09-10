<?php

namespace App\Actions\Quotations;

use App\Enums\ProjectStatus;
use App\Enums\QuotationStatus;
use App\Models\Project;
use App\Models\Quotation;
use App\Support\ReferenceGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Turns an approved quotation into a live project, copying across the
 * commercial context so the user never re-types it. Idempotent: if the
 * quotation already has a project, that project is returned unchanged.
 */
class ConvertQuotationToProject
{
    public function __construct(private readonly ReferenceGenerator $references) {}

    public function handle(Quotation $quotation): Project
    {
        if ($quotation->status !== QuotationStatus::Approved) {
            throw ValidationException::withMessages([
                'status' => 'Only an approved quotation can be converted to a project.',
            ]);
        }

        if ($quotation->project_id !== null) {
            return $quotation->project;
        }

        return DB::transaction(function () use ($quotation): Project {
            $project = Project::create([
                'reference' => $this->references->projectReference(),
                'name' => $quotation->title ?: $quotation->customer->company_name.' project',
                'customer_id' => $quotation->customer_id,
                'lead_id' => $quotation->created_by,
                'customer_pic_name' => $quotation->customer->contact_person,
                'customer_pic_phone' => $quotation->customer->phone,
                'description' => $quotation->notes,
                'status' => ProjectStatus::Planning,
                'priority' => 'medium',
                'progress' => 0,
                'start_date' => now()->toDateString(),
                'value' => $quotation->total,
                'budget' => round((float) $quotation->total * 0.65, 2),
            ]);

            $quotation->update(['project_id' => $project->id]);

            return $project;
        });
    }
}
