<?php

namespace App\Livewire\Drawings;

use App\Models\Drawing;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DrawingEditor extends Component
{
    public Drawing $drawing;

    public string $title = '';

    public function mount(Drawing $drawing): void
    {
        $this->authorize('view', $drawing);
        $this->drawing = $drawing;
        $this->title = $drawing->title;
    }

    public function updatedTitle(string $value): void
    {
        $this->authorize('update', $this->drawing);

        $this->validate(['title' => ['required', 'string', 'max:255']]);

        $this->drawing->update(['title' => $value, 'updated_by' => auth()->id()]);
    }

    public function render(): View
    {
        return view('livewire.drawings.drawing-editor', [
            'title' => $this->drawing->title,
            'canEdit' => auth()->user()->can('update', $this->drawing),
            'canExport' => auth()->user()->can('export', $this->drawing),
        ]);
    }
}
