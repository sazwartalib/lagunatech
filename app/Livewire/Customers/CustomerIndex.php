<?php

namespace App\Livewire\Customers;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Customers')]
class CustomerIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: 'company_name')]
    public string $sort = 'company_name';

    #[Url(except: 'asc')]
    public string $direction = 'asc';

    public function mount(): void
    {
        $this->authorize('viewAny', Customer::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $column): void
    {
        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sort = $column;
        $this->direction = 'asc';
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status');
        $this->resetPage();
    }

    public function render(): View
    {
        $sortable = ['company_name', 'reference', 'status', 'created_at'];
        $sort = in_array($this->sort, $sortable, true) ? $this->sort : 'company_name';
        $direction = $this->direction === 'desc' ? 'desc' : 'asc';

        $customers = Customer::query()
            ->search($this->search)
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->with('accountManager:id,name')
            ->withCount('projects')
            ->orderBy($sort, $direction)
            ->orderBy('id')
            ->paginate(12);

        return view('livewire.customers.customer-index', [
            'customers' => $customers,
            'statuses' => CustomerStatus::options(),
            'hasFilters' => $this->search !== '' || $this->status !== '',
        ]);
    }
}
