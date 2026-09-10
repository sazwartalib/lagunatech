<?php

namespace App\Livewire\Quotations;

use App\Enums\QuotationStatus;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Quotations')]
class QuotationIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Quotation::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $quotations = Quotation::query()
            ->search($this->search)
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->with('customer:id,company_name')
            ->latest()
            ->paginate(12);

        $totals = [
            'pending' => Quotation::query()->pending()->count(),
            'pending_value' => (float) Quotation::query()->pending()->sum('total'),
            'approved_value' => (float) Quotation::query()->where('status', QuotationStatus::Approved->value)->sum('total'),
        ];

        return view('livewire.quotations.quotation-index', [
            'quotations' => $quotations,
            'statuses' => QuotationStatus::options(),
            'totals' => $totals,
            'hasFilters' => $this->search !== '' || $this->status !== '',
        ]);
    }
}
