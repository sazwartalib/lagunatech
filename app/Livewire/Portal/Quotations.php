<?php

namespace App\Livewire\Portal;

use App\Actions\Quotations\TransitionQuotationStatus;
use App\Enums\QuotationStatus;
use App\Livewire\Portal\Concerns\InteractsWithPortalCustomer;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Quotations')]
class Quotations extends Component
{
    use InteractsWithPortalCustomer;

    public ?int $selectedId = null;

    public function view(int $id, TransitionQuotationStatus $transition): void
    {
        $quotation = $this->customer()->quotations()->findOrFail($id);
        $this->selectedId = $quotation->id;

        // Opening a sent quotation marks it as viewed.
        if ($quotation->status === QuotationStatus::Sent) {
            $transition->handle($quotation, QuotationStatus::Viewed);
        }
    }

    public function decide(string $decision, TransitionQuotationStatus $transition): void
    {
        abort_unless(in_array($decision, ['approved', 'rejected'], true), 400);

        $quotation = $this->customer()->quotations()->findOrFail($this->selectedId);

        abort_unless(
            in_array($quotation->status, [QuotationStatus::Sent, QuotationStatus::Viewed], true),
            403,
        );

        $transition->handle($quotation, QuotationStatus::from($decision));
        $this->dispatch('toast', message: $decision === 'approved' ? 'Quotation approved. Thank you!' : 'Quotation rejected.');
    }

    public function render(): View
    {
        $quotations = $this->customer()->quotations()->with('items')->latest()->get();

        return view('livewire.portal.quotations', [
            'quotations' => $quotations,
            'selected' => $this->selectedId ? $quotations->firstWhere('id', $this->selectedId) : null,
        ]);
    }
}
