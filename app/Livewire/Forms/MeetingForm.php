<?php

namespace App\Livewire\Forms;

use App\Models\Meeting;
use Illuminate\Validation\Rule;
use Livewire\Form;

class MeetingForm extends Form
{
    public ?int $meetingId = null;

    public string $title = '';

    public ?int $project_id = null;

    public ?int $customer_id = null;

    public ?string $scheduled_at = null;

    public int $duration_minutes = 60;

    public string $location = '';

    public string $agenda = '';

    /** @var list<int> */
    public array $participant_ids = [];

    /** @var list<array{description: string, owner_id: int|null, due_date: string|null}> */
    public array $action_items = [];

    public function setMeeting(Meeting $meeting): void
    {
        $this->meetingId = $meeting->id;
        $this->title = $meeting->title;
        $this->project_id = $meeting->project_id;
        $this->customer_id = $meeting->customer_id;
        $this->scheduled_at = $meeting->scheduled_at->format('Y-m-d\TH:i');
        $this->duration_minutes = $meeting->duration_minutes;
        $this->location = (string) $meeting->location;
        $this->agenda = (string) $meeting->agenda;
        $this->participant_ids = $meeting->participants->pluck('user_id')->filter()->values()->all();
        $this->action_items = $meeting->actionItems
            ->map(fn ($a) => [
                'description' => $a->description,
                'owner_id' => $a->owner_id,
                'due_date' => $a->due_date?->toDateString(),
            ])->all();
    }

    public function addActionItem(): void
    {
        $this->action_items[] = ['description' => '', 'owner_id' => null, 'due_date' => null];
    }

    public function removeActionItem(int $index): void
    {
        unset($this->action_items[$index]);
        $this->action_items = array_values($this->action_items);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
            'customer_id' => ['nullable', Rule::exists('customers', 'id')],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'location' => ['nullable', 'string', 'max:255'],
            'agenda' => ['nullable', 'string', 'max:10000'],
            'participant_ids' => ['array'],
            'participant_ids.*' => [Rule::exists('users', 'id')],
            'action_items' => ['array'],
            'action_items.*.description' => ['required_with:action_items.*.owner_id', 'nullable', 'string', 'max:255'],
            'action_items.*.owner_id' => ['nullable', Rule::exists('users', 'id')],
            'action_items.*.due_date' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array{data: array<string, mixed>, participant_ids: list<int>, action_items: list<array<string, mixed>>}
     */
    public function validatedData(): array
    {
        $validated = $this->validate();

        $participantIds = array_values(array_map('intval', $validated['participant_ids'] ?? []));
        $actionItems = array_values(array_filter(
            $validated['action_items'] ?? [],
            fn ($item) => filled($item['description'] ?? null),
        ));

        unset($validated['participant_ids'], $validated['action_items'], $validated['meetingId']);

        return ['data' => $validated, 'participant_ids' => $participantIds, 'action_items' => $actionItems];
    }
}
