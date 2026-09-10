<?php

namespace App\Livewire\Portal;

use App\Livewire\Portal\Concerns\InteractsWithPortalCustomer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Home')]
class Dashboard extends Component
{
    use InteractsWithPortalCustomer;

    public function render(): View
    {
        $customer = $this->customer();

        return view('livewire.portal.dashboard', [
            'customer' => $customer,
            'projects' => $customer->projects()->latest()->take(6)->get(),
            'pendingQuotations' => $customer->quotations()->whereIn('status', ['sent', 'viewed'])->count(),
            'outstanding' => (float) $customer->invoices()
                ->whereNotIn('status', ['paid', 'cancelled', 'draft'])
                ->sum(DB::raw('total - amount_paid')),
            'openTickets' => $customer->supportTickets()->whereNotIn('status', ['resolved', 'closed'])->count(),
        ]);
    }
}
