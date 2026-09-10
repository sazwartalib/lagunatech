<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class CustomerShow extends Component
{
    public Customer $customer;

    #[Url]
    public string $tab = 'overview';

    public function mount(Customer $customer): void
    {
        $this->authorize('view', $customer);
        $this->customer = $customer;
    }

    public function render(): View
    {
        $this->customer->loadMissing([
            'accountManager:id,name',
            'contacts',
            'projects' => fn ($q) => $q->withCount('tasks')->latest(),
            'communications' => fn ($q) => $q->with('user:id,name')->limit(30),
        ]);

        $projectValue = (float) $this->customer->projects->sum('value');

        return view('livewire.customers.customer-show', [
            'title' => $this->customer->company_name,
            'projectValue' => $projectValue,
            'financials' => [
                'invoiced' => (float) $this->customer->invoices()->whereNot('status', 'cancelled')->sum('total'),
                'paid' => (float) $this->customer->payments()->sum('amount'),
                'outstanding' => (float) $this->customer->invoices()->whereNotIn('status', ['paid', 'cancelled'])->sum(DB::raw('total - amount_paid')),
            ],
            'activities' => $this->customer->activitiesAsSubject()->with('causer:id,name')->latest()->limit(30)->get(),
        ]);
    }
}
