<?php

namespace App\Livewire\Maintenance;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenancePlan;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Maintenance')]
class MaintenanceIndex extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', MaintenancePlan::class);
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $plans = MaintenancePlan::query()
            ->with(['customer:id,company_name', 'project:id,name'])
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->orderByRaw('next_renewal_on is null, next_renewal_on asc')
            ->paginate(15);

        $active = MaintenancePlan::query()->active()->get(['billing_cycle', 'fee']);

        return view('livewire.maintenance.maintenance-index', [
            'plans' => $plans,
            'statuses' => MaintenanceStatus::options(),
            'annualRecurring' => $active->sum(fn ($p) => $p->annual_value),
            'renewingSoon' => MaintenancePlan::query()->renewingWithin(30)->count(),
        ]);
    }
}
