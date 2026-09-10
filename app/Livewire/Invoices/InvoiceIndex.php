<?php

namespace App\Livewire\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Invoices')]
class InvoiceIndex extends Component
{
    use WithPagination;

    /** Restrict to invoices with an outstanding balance ("Outstanding" route). */
    public bool $outstandingOnly = false;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(bool $outstandingOnly = false): void
    {
        $this->authorize('viewAny', Invoice::class);
        $this->outstandingOnly = $outstandingOnly;
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $query = Invoice::query()
            ->search($this->search)
            ->with('customer:id,company_name')
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->outstandingOnly, fn (Builder $q) => $q->open()->whereColumn('amount_paid', '<', 'total'))
            ->latest();

        $invoices = $query->paginate(12);

        $summary = [
            'outstanding' => (float) Invoice::query()->open()->sum(DB::raw('total - amount_paid')),
            'overdue' => (float) Invoice::query()->overdue()->sum(DB::raw('total - amount_paid')),
            'paid_this_month' => (float) Payment::query()->inMonth()->sum('amount'),
        ];

        return view('livewire.invoices.invoice-index', [
            'invoices' => $invoices,
            'statuses' => InvoiceStatus::options(),
            'summary' => $summary,
            'hasFilters' => $this->search !== '' || $this->status !== '',
        ]);
    }
}
