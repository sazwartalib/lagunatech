<?php

namespace App\Livewire\Forms;

use App\Enums\ChangeRequestStatus;
use App\Models\ChangeRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ChangeRequestForm extends Form
{
    public ?int $changeRequestId = null;

    public ?int $project_id = null;

    public string $title = '';

    public string $description = '';

    public ?string $estimated_cost = null;

    public ?int $additional_days = null;

    public ?int $assigned_to = null;

    public string $status = 'pending';

    public function setChangeRequest(ChangeRequest $cr): void
    {
        $this->changeRequestId = $cr->id;
        $this->project_id = $cr->project_id;
        $this->title = $cr->title;
        $this->description = (string) $cr->description;
        $this->estimated_cost = $cr->estimated_cost !== null ? (string) $cr->estimated_cost : null;
        $this->additional_days = $cr->additional_days;
        $this->assigned_to = $cr->assigned_to;
        $this->status = $cr->status->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', Rule::exists('projects', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'additional_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'assigned_to' => ['nullable', Rule::exists('users', 'id')],
            'status' => ['required', new Enum(ChangeRequestStatus::class)],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = $this->validate();
        unset($data['changeRequestId']);

        return $data;
    }
}
