<?php

namespace App\Livewire\Drawings;

use App\Enums\DrawingStatus;
use App\Models\Drawing;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Drawings')]
class DrawingIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Drawing::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $drawings = Drawing::query()
            ->with('drawable', 'creator:id,name', 'updater:id,name')
            ->when($this->search !== '', fn (Builder $q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->status !== '', fn (Builder $q) => $q->where('status', $this->status))
            ->latest('updated_at')
            ->paginate(15);

        return view('livewire.drawings.drawing-index', [
            'drawings' => $drawings,
            'statuses' => DrawingStatus::options(),
        ]);
    }
}
