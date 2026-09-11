<?php

namespace App\Livewire\Leads;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Leads')]
class LeadIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: 'false')]
    public bool $openOnly = false;

    public function mount(): void
    {
        $this->authorize('viewAny', Lead::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'openOnly'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $leads = Lead::query()
            ->with('owner:id,name')
            ->search($this->search)
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->openOnly, fn (Builder $q) => $q->open())
            ->latest()
            ->paginate(15);

        return view('livewire.leads.lead-index', [
            'leads' => $leads,
            'statuses' => LeadStatus::options(),
            'newCount' => Lead::query()->where('status', LeadStatus::New->value)->count(),
        ]);
    }
}
