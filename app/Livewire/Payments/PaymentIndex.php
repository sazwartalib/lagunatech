<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Payments')]
class PaymentIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Payment::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $payments = Payment::query()
            ->with(['invoice:id,reference,customer_id', 'invoice.customer:id,company_name', 'recorder:id,name'])
            ->when($this->search !== '', function ($q): void {
                $q->where('reference', 'like', "%{$this->search}%")
                    ->orWhere('reference_number', 'like', "%{$this->search}%")
                    ->orWhereHas('invoice', fn ($i) => $i->where('reference', 'like', "%{$this->search}%"))
                    ->orWhereHas('invoice.customer', fn ($c) => $c->where('company_name', 'like', "%{$this->search}%"));
            })
            ->latest('paid_on')
            ->latest('id')
            ->paginate(15);

        return view('livewire.payments.payment-index', [
            'payments' => $payments,
            'thisMonth' => (float) Payment::query()->inMonth()->sum('amount'),
            'thisYear' => (float) Payment::query()
                ->whereBetween('paid_on', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()])
                ->sum('amount'),
            'byMethod' => Payment::query()
                ->select('method', DB::raw('sum(amount) as total'))
                ->groupBy('method')
                ->pluck('total', 'method'),
        ]);
    }
}
