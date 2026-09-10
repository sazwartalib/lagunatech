<?php

namespace App\Livewire\Support;

use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Support Tickets')]
class TicketIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: 'false')]
    public bool $mineOnly = false;

    public function mount(): void
    {
        $this->authorize('viewAny', SupportTicket::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'mineOnly'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $tickets = SupportTicket::query()
            ->with(['customer:id,company_name', 'assignee:id,name'])
            ->when($this->search !== '', fn (Builder $q) => $q->where(fn (Builder $inner) => $inner
                ->where('reference', 'like', "%{$this->search}%")
                ->orWhere('subject', 'like', "%{$this->search}%")))
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->mineOnly, fn (Builder $q) => $q->where('assigned_to', auth()->id()))
            ->orderByRaw("case when status in ('resolved','closed') then 1 else 0 end")
            ->latest()
            ->paginate(15);

        return view('livewire.support.ticket-index', [
            'tickets' => $tickets,
            'statuses' => SupportTicketStatus::options(),
            'openCount' => SupportTicket::query()->open()->count(),
        ]);
    }
}
