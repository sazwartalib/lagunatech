<?php

namespace App\Livewire\Portal;

use App\Livewire\Portal\Concerns\InteractsWithPortalCustomer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Projects')]
class Projects extends Component
{
    use InteractsWithPortalCustomer;

    public function render(): View
    {
        return view('livewire.portal.projects', [
            'projects' => $this->customer()->projects()->latest()->get(),
        ]);
    }
}
