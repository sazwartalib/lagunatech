<?php

namespace App\Livewire\Portal;

use App\Livewire\Portal\Concerns\InteractsWithPortalCustomer;
use App\Support\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Invoices')]
class Invoices extends Component
{
    use InteractsWithPortalCustomer;

    public function render(Settings $settings): View
    {
        $customer = $this->customer();

        // Draft invoices are never shown to customers.
        $invoices = $customer->invoices()
            ->whereNot('status', 'draft')
            ->with('payments')
            ->latest()
            ->get();

        return view('livewire.portal.invoices', [
            'invoices' => $invoices,
            'outstanding' => (float) $customer->invoices()
                ->whereNotIn('status', ['paid', 'cancelled', 'draft'])
                ->sum(DB::raw('total - amount_paid')),
            'bankDetails' => (string) $settings->get('company.bank_details'),
        ]);
    }
}
