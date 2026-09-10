<?php

namespace App\Livewire\ChangeRequests;

use App\Enums\ChangeRequestStatus;
use App\Models\ChangeRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Change Requests')]
class ChangeRequestIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', ChangeRequest::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $changeRequests = ChangeRequest::query()
            ->with(['project:id,name,reference', 'customer:id,company_name', 'assignee:id,name'])
            ->when($this->search !== '', fn (Builder $q) => $q->where(function (Builder $inner): void {
                $inner->where('reference', 'like', "%{$this->search}%")
                    ->orWhere('title', 'like', "%{$this->search}%");
            }))
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.change-requests.change-request-index', [
            'changeRequests' => $changeRequests,
            'statuses' => ChangeRequestStatus::options(),
            'awaiting' => ChangeRequest::query()->awaitingAction()->count(),
        ]);
    }
}
