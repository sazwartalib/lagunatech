<?php

namespace App\Livewire\Communications;

use App\Enums\CommunicationType;
use App\Models\Communication;
use App\Models\Customer;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Communication Log')]
class CommunicationIndex extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $customerFilter = '';

    #[Url(except: '')]
    public string $typeFilter = '';

    #[Url(except: 'false')]
    public bool $followUpOnly = false;

    public bool $showForm = false;

    #[Validate('required|exists:customers,id')]
    public ?int $customer_id = null;

    #[Validate('nullable|exists:projects,id')]
    public ?int $project_id = null;

    #[Validate('required')]
    public string $type = 'whatsapp';

    #[Validate('required|date')]
    public string $communicated_at = '';

    #[Validate('required|string|max:5000')]
    public string $summary = '';

    #[Validate('nullable|string|max:2000')]
    public string $action_required = '';

    #[Validate('nullable|date')]
    public ?string $follow_up_on = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Communication::class);
        $this->communicated_at = now()->format('Y-m-d\TH:i');
        $this->customer_id = request()->integer('customer') ?: null;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['customerFilter', 'typeFilter', 'followUpOnly'], true)) {
            $this->resetPage();
        }
    }

    public function save(): void
    {
        $this->authorize('create', Communication::class);
        $data = $this->validate();

        Communication::create([
            ...$data,
            'user_id' => auth()->id(),
            'action_required' => $this->action_required ?: null,
            'follow_up_on' => $this->follow_up_on ?: null,
        ]);

        $this->reset('summary', 'action_required', 'follow_up_on', 'project_id', 'showForm');
        $this->communicated_at = now()->format('Y-m-d\TH:i');
        $this->dispatch('toast', message: 'Communication logged.');
    }

    public function toggleFollowUp(int $id): void
    {
        $communication = Communication::findOrFail($id);
        $this->authorize('update', $communication);
        $communication->update(['follow_up_done' => ! $communication->follow_up_done]);
    }

    public function render(): View
    {
        $communications = Communication::query()
            ->with(['customer:id,company_name', 'project:id,name', 'user:id,name'])
            ->when($this->customerFilter !== '', fn (Builder $q) => $q->where('customer_id', $this->customerFilter))
            ->when($this->typeFilter !== '', fn (Builder $q) => $q->where('type', $this->typeFilter))
            ->when($this->followUpOnly, fn (Builder $q) => $q->needsFollowUp())
            ->latest('communicated_at')
            ->paginate(15);

        return view('livewire.communications.communication-index', [
            'communications' => $communications,
            'types' => CommunicationType::options(),
            'customers' => Customer::query()->orderBy('company_name')->get(['id', 'company_name']),
            'projects' => Project::query()->orderBy('name')->get(['id', 'name', 'customer_id']),
            'pendingFollowUps' => Communication::query()->needsFollowUp()->count(),
        ]);
    }
}
