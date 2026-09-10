<?php

namespace App\Livewire\Documents;

use App\Actions\Documents\StoreDocument;
use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentManager extends Component
{
    use WithFileUploads;

    public Project $project;

    public bool $showUpload = false;

    #[Validate('required|file|max:20480')] // 20 MB
    public $file = null;

    #[Validate('required|string')]
    public string $category = 'other';

    #[Validate('nullable|string|max:255')]
    public string $docTitle = '';

    public bool $isInternal = true;

    public function upload(StoreDocument $store): void
    {
        $this->authorize('create', Document::class);
        $this->validate();

        $store->handle($this->project, $this->file, $this->category, $this->docTitle ?: null, $this->isInternal);

        $this->reset('file', 'docTitle', 'showUpload');
        $this->category = 'other';
        $this->isInternal = true;
        $this->dispatch('toast', message: 'Document uploaded.');
    }

    public function deleteDocument(int $documentId): void
    {
        $document = $this->project->documents()->findOrFail($documentId);
        $this->authorize('delete', $document);
        $document->delete();
        $this->dispatch('toast', message: 'Document deleted.', type: 'info');
    }

    public function render(): View
    {
        return view('livewire.documents.document-manager', [
            'documents' => $this->project->documents()->with('uploader:id,name')->get()->groupBy(fn (Document $d) => $d->category->value),
            'categories' => DocumentCategory::cases(),
        ]);
    }
}
