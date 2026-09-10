<?php

namespace App\Livewire\Maintenance;

use App\Actions\Maintenance\UpsertMaintenancePlan;
use App\Enums\BillingCycle;
use App\Enums\MaintenanceStatus;
use App\Models\Customer;
use App\Models\MaintenancePlan;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class MaintenanceForm extends Component
{
    public ?MaintenancePlan $plan = null;

    #[Validate('required|exists:customers,id')]
    public ?int $customer_id = null;

    #[Validate('nullable|exists:projects,id')]
    public ?int $project_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:5000')]
    public string $description = '';

    public string $status = 'active';

    public string $billing_cycle = 'monthly';

    #[Validate('required|numeric|min:0')]
    public string $fee = '';

    #[Validate('required|date')]
    public ?string $starts_on = null;

    #[Validate('nullable|date|after_or_equal:starts_on')]
    public ?string $ends_on = null;

    public function mount(?MaintenancePlan $plan = null): void
    {
        if ($plan?->exists) {
            $this->authorize('update', $plan);
            $this->plan = $plan;
            $this->fill([
                'customer_id' => $plan->customer_id,
                'project_id' => $plan->project_id,
                'name' => $plan->name,
                'description' => (string) $plan->description,
                'status' => $plan->status->value,
                'billing_cycle' => $plan->billing_cycle->value,
                'fee' => (string) $plan->fee,
                'starts_on' => $plan->starts_on->toDateString(),
                'ends_on' => $plan->ends_on?->toDateString(),
            ]);

            return;
        }

        $this->authorize('create', MaintenancePlan::class);
        $this->starts_on = now()->toDateString();
        $this->customer_id = request()->integer('customer') ?: null;
        $this->project_id = request()->integer('project') ?: null;
    }

    public function save(UpsertMaintenancePlan $upsert): void
    {
        $this->validate([
            'status' => ['required', new Enum(MaintenanceStatus::class)],
            'billing_cycle' => ['required', new Enum(BillingCycle::class)],
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
        ]);

        $plan = $upsert->handle([
            'customer_id' => $this->customer_id,
            'project_id' => $this->project_id ?: null,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'status' => $this->status,
            'billing_cycle' => $this->billing_cycle,
            'fee' => $this->fee,
            'starts_on' => $this->starts_on,
            'ends_on' => $this->ends_on ?: null,
        ], $this->plan);

        session()->flash('status', $this->plan ? 'Maintenance plan updated.' : 'Maintenance plan created.');
        $this->redirectRoute('maintenance.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.maintenance.maintenance-form', [
            'title' => $this->plan ? 'Edit maintenance plan' : 'New maintenance plan',
            'customers' => Customer::query()->orderBy('company_name')->get(['id', 'company_name']),
            'projects' => Project::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
