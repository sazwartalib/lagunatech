<?php

namespace App\Livewire\Forms;

use App\Enums\BugStatus;
use App\Enums\Priority;
use App\Models\Bug;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class BugForm extends Form
{
    public ?int $bugId = null;

    public ?int $project_id = null;

    public string $title = '';

    public string $description = '';

    public ?int $assigned_to = null;

    public string $priority = 'medium';

    public string $status = 'open';

    public ?string $due_date = null;

    public function setBug(Bug $bug): void
    {
        $this->bugId = $bug->id;
        $this->project_id = $bug->project_id;
        $this->title = $bug->title;
        $this->description = (string) $bug->description;
        $this->assigned_to = $bug->assigned_to;
        $this->priority = $bug->priority->value;
        $this->status = $bug->status->value;
        $this->due_date = $bug->due_date?->toDateString();
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
            'assigned_to' => ['nullable', Rule::exists('users', 'id')],
            'priority' => ['required', new Enum(Priority::class)],
            'status' => ['required', new Enum(BugStatus::class)],
            'due_date' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        $data = $this->validate();
        unset($data['bugId']);

        return $data;
    }
}
