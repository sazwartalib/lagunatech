<?php

namespace App\Livewire\Drawings;

use App\Actions\Drawings\CreateDrawing;
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

    public bool $showCreate = false;

    public string $newTitle = '';

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

    public function create(CreateDrawing $create): void
    {
        $this->authorize('create', Drawing::class);

        $validated = $this->validate(['newTitle' => ['required', 'string', 'max:255']]);

        $drawing = $create->handle(null, $validated['newTitle']);

        // Full page load, not wire:navigate — the canvas editor's JS module
        // must execute fresh; Livewire's SPA navigation won't run a newly
        // inserted <script type="module"> tag.
        $this->redirect(route('drawings.show', $drawing));
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
