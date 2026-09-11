<?php

namespace App\Livewire\Drawings;

use App\Actions\Drawings\CreateDrawing;
use App\Models\Drawing;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class DrawingManager extends Component
{
    public Model $drawable;

    public bool $showCreate = false;

    public string $newTitle = '';

    public function create(CreateDrawing $create): void
    {
        $this->authorize('create', Drawing::class);

        $validated = $this->validate(['newTitle' => ['required', 'string', 'max:255']]);

        $drawing = $create->handle($this->drawable, $validated['newTitle']);

        // A full page load, not wire:navigate — the canvas editor's JS module
        // must execute fresh; Livewire's SPA navigation won't run a newly
        // inserted <script type="module"> tag.
        $this->redirect(route('drawings.show', $drawing));
    }

    public function deleteDrawing(int $drawingId): void
    {
        $drawing = $this->drawable->drawings()->findOrFail($drawingId);
        $this->authorize('delete', $drawing);

        $drawing->delete();
        $this->dispatch('toast', message: 'Drawing deleted.', type: 'info');
    }

    public function render(): View
    {
        return view('livewire.drawings.drawing-manager', [
            'drawings' => $this->drawable->drawings()->with('updater:id,name')->get(),
        ]);
    }
}
