<?php

namespace App\Livewire\Forms;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ProjectForm extends Form
{
    public ?int $projectId = null;

    public string $name = '';

    public ?int $customer_id = null;

    public ?int $lead_id = null;

    public string $customer_pic_name = '';

    public string $customer_pic_phone = '';

    public string $type = '';

    public string $technology = '';

    public string $description = '';

    public string $status = 'lead';

    public string $priority = 'medium';

    public int $progress = 0;

    public ?string $start_date = null;

    public ?string $target_end_date = null;

    public ?string $budget = null;

    public ?string $value = null;

    public string $notes = '';

    /** @var list<int> */
    public array $member_ids = [];

    public function setProject(Project $project): void
    {
        $this->projectId = $project->id;
        $this->name = $project->name;
        $this->customer_id = $project->customer_id;
        $this->lead_id = $project->lead_id;
        $this->customer_pic_name = (string) $project->customer_pic_name;
        $this->customer_pic_phone = (string) $project->customer_pic_phone;
        $this->type = (string) $project->type;
        $this->technology = (string) $project->technology;
        $this->description = (string) $project->description;
        $this->status = $project->status->value;
        $this->priority = $project->priority->value;
        $this->progress = (int) $project->progress;
        $this->start_date = $project->start_date?->toDateString();
        $this->target_end_date = $project->target_end_date?->toDateString();
        $this->budget = $project->budget !== null ? (string) $project->budget : null;
        $this->value = $project->value !== null ? (string) $project->value : null;
        $this->notes = (string) $project->notes;
        $this->member_ids = $project->members->pluck('id')->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'customer_id' => ['required', Rule::exists('customers', 'id')],
            'lead_id' => ['nullable', Rule::exists('users', 'id')],
            'customer_pic_name' => ['nullable', 'string', 'max:255'],
            'customer_pic_phone' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:100'],
            'technology' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', new Enum(ProjectStatus::class)],
            'priority' => ['required', new Enum(Priority::class)],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'target_end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'member_ids' => ['array'],
            'member_ids.*' => [Rule::exists('users', 'id')],
        ];
    }

    /**
     * @return array{data: array<string, mixed>, member_ids: list<int>}
     */
    public function validatedData(): array
    {
        $validated = $this->validate();

        $memberIds = array_map('intval', $validated['member_ids'] ?? []);
        unset($validated['projectId'], $validated['member_ids']);

        return ['data' => $validated, 'member_ids' => $memberIds];
    }
}
